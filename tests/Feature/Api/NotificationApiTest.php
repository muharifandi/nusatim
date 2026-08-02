<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_partner_can_list_their_own_notifications(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Notification::make()->title('Lead baru masuk')->sendToDatabase($partner);
        Notification::make()->title('Untuk partner lain')->sendToDatabase($other);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/notifications');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Lead baru masuk');
    }

    public function test_unread_count_reflects_unread_notifications_only(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Notification::make()->title('Satu')->sendToDatabase($partner);
        Notification::make()->title('Dua')->sendToDatabase($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('unread_count', 2);
    }

    public function test_mark_read_marks_a_single_notification(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        Notification::make()->title('Satu')->sendToDatabase($partner);
        $notificationId = $partner->notifications()->first()->id;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/notifications/{$notificationId}/read");

        $response->assertOk();
        $this->assertNotNull($response->json('data.read_at'));
        $this->assertSame(0, $partner->unreadNotifications()->count());
    }

    public function test_mark_all_read_marks_every_notification(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        Notification::make()->title('Satu')->sendToDatabase($partner);
        Notification::make()->title('Dua')->sendToDatabase($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson('/api/v1/notifications/read-all')
            ->assertOk();

        $this->assertSame(0, $partner->unreadNotifications()->count());
    }

    public function test_cannot_mark_another_partners_notification_as_read(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        Notification::make()->title('Punya Orang Lain')->sendToDatabase($other);
        $notificationId = $other->notifications()->first()->id;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/notifications/{$notificationId}/read")
            ->assertNotFound();
    }
}
