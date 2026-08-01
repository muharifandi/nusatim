<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_pending_partner_can_still_view_and_update_their_own_profile(): void
    {
        $partner = Partner::factory()->create(['status' => 'pending_review', 'name' => 'Budi']);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.name', 'Budi');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/profile', ['name' => 'Budi Santoso'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Budi Santoso');

        $this->assertSame('Budi Santoso', $partner->fresh()->name);
    }

    public function test_updating_email_to_one_already_taken_fails_validation(): void
    {
        Partner::factory()->create(['email' => 'taken@example.com']);
        $partner = Partner::factory()->create(['email' => 'me@example.com']);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/profile', ['email' => 'taken@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_partner_can_replace_profile_photo_and_old_file_is_deleted(): void
    {
        Storage::fake('partner_documents');
        $partner = Partner::factory()->create(['profile_photo_path' => 'profile/old-photo.jpg']);
        Storage::disk('partner_documents')->put('profile/old-photo.jpg', 'old-content');
        $token = $this->tokenFor($partner);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post('/api/v1/profile/photo', [
                'profile_photo' => UploadedFile::fake()->image('new-photo.jpg'),
            ]);

        $response->assertOk();

        $partner->refresh();
        $this->assertNotSame('profile/old-photo.jpg', $partner->profile_photo_path);
        Storage::disk('partner_documents')->assertExists($partner->profile_photo_path);
        Storage::disk('partner_documents')->assertMissing('profile/old-photo.jpg');
    }

    public function test_partner_can_stream_their_own_document(): void
    {
        Storage::fake('partner_documents');
        Storage::disk('partner_documents')->put('registrations/ktp.jpg', 'ktp-content');
        $partner = Partner::factory()->create(['ktp_path' => 'registrations/ktp.jpg']);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->get('/api/v1/profile/documents/ktp')
            ->assertOk();
    }

    public function test_partner_cannot_stream_an_unknown_document_type(): void
    {
        $partner = Partner::factory()->create();
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->get('/api/v1/profile/documents/passport')
            ->assertNotFound();
    }

    public function test_change_password_requires_correct_current_password(): void
    {
        $partner = Partner::factory()->create(['password' => Hash::make('old-password')]);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_change_password_succeeds_and_new_password_can_login(): void
    {
        $partner = Partner::factory()->create([
            'email' => 'budi@example.com',
            'password' => Hash::make('old-password'),
        ]);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ])
            ->assertOk();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'new-password123',
        ])->assertOk();
    }
}
