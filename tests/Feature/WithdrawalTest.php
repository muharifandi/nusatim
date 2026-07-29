<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WithdrawalTest extends TestCase
{
    use RefreshDatabase;

    protected function approvedCommission(Partner $partner, float $amount): Commission
    {
        $customer = Customer::create([
            'partner_id' => $partner->id,
            'name' => 'Customer '.uniqid(),
            'project_value' => $amount,
        ]);

        $commission = Commission::create([
            'customer_id' => $customer->id,
            'partner_id' => $partner->id,
            'amount' => $amount,
            'type' => 'flat',
            'status' => 'approved',
        ]);

        return $commission;
    }

    public function test_submitting_more_than_the_available_balance_is_rejected(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->approvedCommission($partner, 500000);

        $this->expectException(ValidationException::class);

        Withdrawal::submit($partner, ['amount' => 1000000, 'ktp_path' => 'withdrawals/ktp.jpg']);
    }

    public function test_submitting_below_the_minimum_withdrawal_is_rejected(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->approvedCommission($partner, 500000);
        PartnerSetting::current()->update(['minimum_withdrawal' => 200000]);

        $this->expectException(ValidationException::class);

        Withdrawal::submit($partner, ['amount' => 100000, 'ktp_path' => 'withdrawals/ktp.jpg']);
    }

    public function test_submitting_a_valid_withdrawal_snapshots_the_partners_bank_details(): void
    {
        $partner = Partner::factory()->create([
            'status' => 'approved',
            'bank_name' => 'BCA',
            'bank_account_number' => '123456',
            'bank_account_holder' => 'Budi',
        ]);
        $this->approvedCommission($partner, 1000000);

        $withdrawal = Withdrawal::submit($partner, [
            'amount' => 500000,
            'ktp_path' => 'withdrawals/ktp.jpg',
            'note' => 'Butuh cepat',
        ]);

        $this->assertSame('pending', $withdrawal->status);
        $this->assertSame('BCA', $withdrawal->bank_name);
        $this->assertSame('123456', $withdrawal->bank_account_number);

        // Changing the partner's bank details afterwards must not affect
        // the already-submitted withdrawal's snapshot.
        $partner->update(['bank_name' => 'Mandiri']);
        $this->assertSame('BCA', $withdrawal->fresh()->bank_name);
    }

    public function test_approving_a_withdrawal_is_logged_to_the_audit_trail(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->approvedCommission($partner, 500000);
        $withdrawal = Withdrawal::submit($partner, ['amount' => 300000, 'ktp_path' => 'withdrawals/ktp.jpg']);

        $withdrawal->approve();

        $this->assertSame('approved', $withdrawal->fresh()->status);
        $this->assertSame(1, $withdrawal->histories()->count());
        $this->assertSame('pending', $withdrawal->histories()->first()->from_status);
        $this->assertSame('approved', $withdrawal->histories()->first()->to_status);
    }

    public function test_marking_paid_consumes_approved_commissions_fifo_until_covered(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $commissionA = $this->approvedCommission($partner, 200000);
        $commissionB = $this->approvedCommission($partner, 200000);
        $commissionC = $this->approvedCommission($partner, 200000);

        $withdrawal = Withdrawal::submit($partner, ['amount' => 350000, 'ktp_path' => 'withdrawals/ktp.jpg']);
        $withdrawal->approve();
        $withdrawal->markPaid('withdrawals/proof.jpg');

        // 350k covered by the two oldest commissions (200k + 200k = 400k,
        // the second one isn't split - it's simply marked paid in full).
        $this->assertSame('paid', $commissionA->fresh()->status);
        $this->assertSame('paid', $commissionB->fresh()->status);
        $this->assertSame('approved', $commissionC->fresh()->status);
        $this->assertSame('paid', $withdrawal->fresh()->status);
        $this->assertSame('withdrawals/proof.jpg', $withdrawal->fresh()->proof_of_transfer_path);
    }

    public function test_partner_cannot_see_or_access_another_partners_withdrawal_documents(): void
    {
        $owner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $this->approvedCommission($owner, 500000);

        $withdrawal = Withdrawal::submit($owner, ['amount' => 100000, 'ktp_path' => 'withdrawals/ktp.jpg']);

        \Illuminate\Support\Facades\Storage::disk('partner_documents')->put('withdrawals/ktp.jpg', 'fake');

        $this->actingAs($other, 'partner')
            ->get(route('withdrawal.documents.show', [$withdrawal, 'ktp']))
            ->assertForbidden();

        $this->actingAs($owner, 'partner')
            ->get(route('withdrawal.documents.show', [$withdrawal, 'ktp']))
            ->assertOk();
    }

    public function test_withdrawal_pages_render_for_partner_and_admin(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.withdrawals.index'))
            ->assertOk();
        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.withdrawals.create'))
            ->assertOk();

        $admin = User::factory()->create();
        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.resources.withdrawals.index'))
            ->assertOk();
    }
}
