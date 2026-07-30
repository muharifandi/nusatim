<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_model_with_logs_audit_writes_a_created_row(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $log = AuditLog::where('auditable_type', Partner::class)
            ->where('auditable_id', $partner->id)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($log);
    }

    public function test_updating_a_model_logs_the_before_and_after_values(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Test', 'phone' => '0811', 'status' => 'new']);

        $lead->update(['status' => 'contacted']);

        $log = AuditLog::where('auditable_type', Lead::class)
            ->where('auditable_id', $lead->id)
            ->where('action', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('new', $log->changes['before']['status']);
        $this->assertSame('contacted', $log->changes['after']['status']);
    }

    public function test_deleting_a_model_logs_a_deleted_row(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $ticket = SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'x', 'description' => 'x']);
        $ticketId = $ticket->id;

        $ticket->delete();

        $log = AuditLog::where('auditable_type', SupportTicket::class)
            ->where('auditable_id', $ticketId)
            ->where('action', 'deleted')
            ->first();

        $this->assertNotNull($log);
    }

    public function test_actor_is_captured_for_changes_made_by_a_logged_in_staff_user(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $partner = Partner::factory()->create(['status' => 'approved']);
        $partner->update(['status' => 'suspended']);

        $log = AuditLog::where('auditable_type', Partner::class)
            ->where('auditable_id', $partner->id)
            ->where('action', 'updated')
            ->latest('id')
            ->first();

        $this->assertSame(User::class, $log->user_type);
        $this->assertSame($admin->id, $log->user_id);
    }

    public function test_actor_is_captured_for_changes_made_by_a_logged_in_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->actingAs($partner, 'partner');

        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Test', 'phone' => '0811']);
        $lead->update(['name' => 'Lead Diubah']);

        $log = AuditLog::where('auditable_type', Lead::class)
            ->where('auditable_id', $lead->id)
            ->where('action', 'updated')
            ->latest('id')
            ->first();

        $this->assertSame(Partner::class, $log->user_type);
        $this->assertSame($partner->id, $log->user_id);
    }

    public function test_admin_audit_log_page_renders(): void
    {
        $admin = User::factory()->create();
        Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.audit-logs.index'))
            ->assertOk();
    }
}
