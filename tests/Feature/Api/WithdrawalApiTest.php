<?php

namespace Tests\Feature\Api;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WithdrawalApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    protected function approvedCommission(Partner $partner, float $amount): Commission
    {
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer '.uniqid(), 'project_value' => $amount]);

        return Commission::create([
            'customer_id' => $customer->id,
            'partner_id' => $partner->id,
            'amount' => $amount,
            'type' => 'flat',
            'status' => 'approved',
        ]);
    }

    public function test_balance_endpoint_reports_available_balance_and_minimum(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $this->approvedCommission($partner, 500000);
        PartnerSetting::current()->update(['minimum_withdrawal' => 100000]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/withdrawals/balance');

        $response->assertOk();
        $response->assertJsonPath('available_balance', 500000);
        $response->assertJsonPath('minimum_withdrawal', 100000);
    }

    public function test_partner_can_submit_a_valid_withdrawal(): void
    {
        Storage::fake('partner_documents');
        $partner = Partner::factory()->create([
            'status' => 'approved',
            'bank_name' => 'BCA',
            'bank_account_number' => '123456',
            'bank_account_holder' => 'Budi',
        ]);
        $token = $this->tokenFor($partner);
        $this->approvedCommission($partner, 1000000);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post('/api/v1/withdrawals', [
                'amount' => 500000,
                'ktp' => UploadedFile::fake()->image('ktp.jpg'),
                'note' => 'Butuh cepat',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('data.amount', 500000);
        $response->assertJsonPath('data.bank_name', 'BCA');
        $response->assertJsonPath('data.status', 'pending');
        $this->assertDatabaseHas('withdrawals', ['partner_id' => $partner->id, 'bank_account_holder' => 'Budi']);
    }

    public function test_submitting_more_than_available_balance_is_rejected(): void
    {
        Storage::fake('partner_documents');
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $this->approvedCommission($partner, 300000);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post('/api/v1/withdrawals', [
                'amount' => 1000000,
                'ktp' => UploadedFile::fake()->image('ktp.jpg'),
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['amount']);
    }

    public function test_submitting_below_minimum_withdrawal_is_rejected(): void
    {
        Storage::fake('partner_documents');
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $this->approvedCommission($partner, 500000);
        PartnerSetting::current()->update(['minimum_withdrawal' => 200000]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post('/api/v1/withdrawals', [
                'amount' => 100000,
                'ktp' => UploadedFile::fake()->image('ktp.jpg'),
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['amount']);
    }

    public function test_partner_can_only_list_their_own_withdrawals(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Withdrawal::create(['partner_id' => $partner->id, 'amount' => 100000, 'bank_name' => 'BCA', 'bank_account_number' => '1', 'bank_account_holder' => 'A', 'ktp_path' => 'x.jpg', 'status' => 'pending']);
        Withdrawal::create(['partner_id' => $other->id, 'amount' => 200000, 'bank_name' => 'BCA', 'bank_account_number' => '2', 'bank_account_holder' => 'B', 'ktp_path' => 'y.jpg', 'status' => 'pending']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/withdrawals');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.amount', 100000);
    }

    public function test_partner_can_stream_their_own_withdrawal_document(): void
    {
        Storage::fake('partner_documents');
        Storage::disk('partner_documents')->put('withdrawals/ktp.jpg', 'content');
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $withdrawal = Withdrawal::create(['partner_id' => $partner->id, 'amount' => 100000, 'bank_name' => 'BCA', 'bank_account_number' => '1', 'bank_account_holder' => 'A', 'ktp_path' => 'withdrawals/ktp.jpg', 'status' => 'pending']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->get("/api/v1/withdrawals/{$withdrawal->id}/documents/ktp")
            ->assertOk();
    }

    public function test_partner_cannot_access_another_partners_withdrawal_document(): void
    {
        Storage::fake('partner_documents');
        Storage::disk('partner_documents')->put('withdrawals/ktp.jpg', 'content');
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $withdrawal = Withdrawal::create(['partner_id' => $other->id, 'amount' => 100000, 'bank_name' => 'BCA', 'bank_account_number' => '1', 'bank_account_holder' => 'A', 'ktp_path' => 'withdrawals/ktp.jpg', 'status' => 'pending']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->get("/api/v1/withdrawals/{$withdrawal->id}/documents/ktp")
            ->assertNotFound();
    }
}
