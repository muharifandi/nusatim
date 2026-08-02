<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_partner_can_create_a_support_ticket(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/support-tickets', [
                'subject' => 'Tidak bisa upload dokumen',
                'description' => 'Setiap upload KTP selalu gagal.',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'open');
        $this->assertDatabaseHas('support_tickets', ['partner_id' => $partner->id, 'subject' => 'Tidak bisa upload dokumen']);
    }

    public function test_partner_can_only_list_their_own_tickets(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'Punya Saya', 'description' => 'x', 'status' => 'open']);
        SupportTicket::create(['partner_id' => $other->id, 'subject' => 'Punya Orang Lain', 'description' => 'y', 'status' => 'open']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/support-tickets');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.subject', 'Punya Saya');
    }

    public function test_show_includes_resolution_note_when_resolved(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $ticket = SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'Butuh Bantuan', 'description' => 'x', 'status' => 'open']);
        $ticket->resolve('Sudah diperbaiki di versi terbaru.');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/support-tickets/{$ticket->id}");

        $response->assertOk();
        $response->assertJsonPath('data.status', 'resolved');
        $response->assertJsonPath('data.resolution_note', 'Sudah diperbaiki di versi terbaru.');
    }

    public function test_viewing_another_partners_ticket_returns_404(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $ticket = SupportTicket::create(['partner_id' => $other->id, 'subject' => 'Bukan Punya Saya', 'description' => 'x', 'status' => 'open']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/support-tickets/{$ticket->id}")
            ->assertNotFound();
    }
}
