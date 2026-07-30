<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadDuplicateDetectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_phone_number_in_different_formats_is_detected_as_duplicate(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create(['partner_id' => $partnerA->id, 'name' => 'PT Alpha', 'phone' => '081234567890']);
        $sameNumberDifferentFormat = Lead::create(['partner_id' => $partnerB->id, 'name' => 'CV Beta', 'phone' => '+62 812-3456-7890']);
        $unrelated = Lead::create(['partner_id' => $partnerB->id, 'name' => 'PT Gamma', 'phone' => '081999999999']);

        $duplicates = $lead->findPotentialDuplicates();

        $this->assertTrue($duplicates->contains('id', $sameNumberDifferentFormat->id));
        $this->assertFalse($duplicates->contains('id', $unrelated->id));
    }

    public function test_same_email_different_case_is_detected_as_duplicate(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'PT Satu', 'phone' => '0811', 'email' => 'Info@Contoh.com']);
        $sameEmailDifferentCase = Lead::create(['partner_id' => $partner->id, 'name' => 'PT Dua', 'phone' => '0822', 'email' => 'info@contoh.com']);

        $duplicates = $lead->findPotentialDuplicates();

        $this->assertTrue($duplicates->contains('id', $sameEmailDifferentCase->id));
    }

    public function test_similar_name_with_typo_is_detected_as_duplicate(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'PT Sumber Makmur Sejahtera', 'phone' => '0811']);
        $typoName = Lead::create(['partner_id' => $partner->id, 'name' => 'PT Sumber Makmur Sejahtra', 'phone' => '0822']);
        $differentName = Lead::create(['partner_id' => $partner->id, 'name' => 'CV Berbeda Total', 'phone' => '0833']);

        $duplicates = $lead->findPotentialDuplicates();

        $this->assertTrue($duplicates->contains('id', $typoName->id));
        $this->assertFalse($duplicates->contains('id', $differentName->id));
    }

    public function test_completely_different_leads_are_not_flagged(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'PT Satu', 'phone' => '0811', 'email' => 'satu@contoh.com']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'CV Dua Beda', 'phone' => '0822', 'email' => 'dua@lain.com']);

        $this->assertCount(0, $lead->findPotentialDuplicates());
    }

    public function test_admin_lead_view_page_shows_duplicate_list(): void
    {
        $admin = User::factory()->create();
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create(['partner_id' => $partnerA->id, 'name' => 'PT Alpha', 'phone' => '081234567890']);
        Lead::create(['partner_id' => $partnerB->id, 'name' => 'PT Alpha', 'phone' => '+6281234567890']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\LeadResource\Pages\ManageLeads::class)
            ->mountTableAction('view', $lead)
            ->assertSee($partnerB->name);
    }
}
