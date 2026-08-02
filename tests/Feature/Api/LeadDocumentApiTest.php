<?php

namespace Tests\Feature\Api;

use App\Models\Lead;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeadDocumentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_partner_can_upload_list_download_and_delete_a_document(): void
    {
        Storage::fake('lead_documents');
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Dok', 'phone' => '0811']);

        $upload = $this->withHeader('Authorization', "Bearer {$token}")
            ->post("/api/v1/leads/{$lead->id}/documents", [
                'file' => UploadedFile::fake()->create('proposal.pdf', 100),
                'original_name' => 'Proposal Final',
            ]);
        $upload->assertCreated();
        $upload->assertJsonPath('data.original_name', 'Proposal Final');
        $documentId = $upload->json('data.id');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/leads/{$lead->id}/documents")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->get("/api/v1/leads/{$lead->id}/documents/{$documentId}/download")
            ->assertOk();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/leads/{$lead->id}/documents/{$documentId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('lead_documents', ['id' => $documentId]);
    }

    public function test_documents_are_scoped_through_lead_ownership(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $otherLead = Lead::create(['partner_id' => $other->id, 'name' => 'Bukan Punya Saya', 'phone' => '0811']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/leads/{$otherLead->id}/documents")
            ->assertNotFound();
    }
}
