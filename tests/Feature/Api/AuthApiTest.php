<?php

namespace Tests\Feature\Api;

use App\Mail\PartnerRegistrationReceived;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_register_with_documents_and_ends_up_pending_review(): void
    {
        Storage::fake('partner_documents');
        Mail::fake();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
            'ktp' => UploadedFile::fake()->image('ktp.jpg'),
            'npwp' => UploadedFile::fake()->image('npwp.jpg'),
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
            'agreement_accepted' => true,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'pending_review');
        $response->assertJsonPath('data.email', 'budi@example.com');

        $partner = Partner::where('email', 'budi@example.com')->first();
        $this->assertNotNull($partner);
        Storage::disk('partner_documents')->assertExists($partner->profile_photo_path);
        Storage::disk('partner_documents')->assertExists($partner->ktp_path);
        Storage::disk('partner_documents')->assertExists($partner->npwp_path);
        Mail::assertSent(PartnerRegistrationReceived::class);
    }

    public function test_registration_fails_validation_without_required_documents(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
            'agreement_accepted' => true,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['profile_photo', 'ktp']);
    }

    public function test_registration_requires_agreement_accepted(): void
    {
        Storage::fake('partner_documents');

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
            'ktp' => UploadedFile::fake()->image('ktp.jpg'),
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['agreement_accepted']);
    }

    public function test_partner_can_login_and_receives_a_token(): void
    {
        $partner = Partner::factory()->create([
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'partner']);
        $this->assertNotEmpty($response->json('token'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => Partner::class,
            'tokenable_id' => $partner->id,
        ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        Partner::factory()->create([
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $partner = Partner::factory()->create();
        $token = $partner->createToken('mobile')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');

        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Within a single test method, sequential HTTP calls share the same
        // AuthManager instance, so the Sanctum RequestGuard would otherwise
        // keep returning its cached user from the call above instead of
        // re-resolving against the (now deleted) token - forgetGuards()
        // forces fresh guard resolution, matching what a real second HTTP
        // request (a genuinely new app boot) would do.
        auth()->forgetGuards();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_me_returns_the_authenticated_partner(): void
    {
        $partner = Partner::factory()->create(['name' => 'Budi Santoso']);
        $token = $partner->createToken('mobile')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $response->assertOk();
        $response->assertJsonPath('data.id', $partner->id);
        $response->assertJsonPath('data.name', 'Budi Santoso');
    }

    public function test_protected_endpoint_rejects_request_without_token(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }
}
