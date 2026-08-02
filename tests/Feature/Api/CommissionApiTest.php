<?php

namespace Tests\Feature\Api;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_partner_can_only_list_their_own_commissions(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Saya']);
        $otherCustomer = Customer::create(['partner_id' => $other->id, 'name' => 'Customer Lain']);

        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 300000, 'type' => 'flat', 'status' => 'pending']);
        Commission::create(['customer_id' => $otherCustomer->id, 'partner_id' => $other->id, 'amount' => 100000, 'type' => 'flat', 'status' => 'pending']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/commissions');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.amount', 300000);
    }

    public function test_index_filters_by_status(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer']);

        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 100000, 'type' => 'flat', 'status' => 'pending']);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 200000, 'type' => 'flat', 'status' => 'approved', 'is_bonus' => true]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/commissions?status=approved');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.status', 'approved');
    }

    public function test_show_includes_customer_and_service_name(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Detail']);
        $commission = Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 150000, 'type' => 'flat', 'status' => 'paid']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/commissions/{$commission->id}");

        $response->assertOk();
        $response->assertJsonPath('data.customer_name', 'Customer Detail');
        $response->assertJsonPath('data.status', 'paid');
    }

    public function test_viewing_another_partners_commission_returns_404(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $customer = Customer::create(['partner_id' => $other->id, 'name' => 'Bukan Punya Saya']);
        $commission = Commission::create(['customer_id' => $customer->id, 'partner_id' => $other->id, 'amount' => 50000, 'type' => 'flat', 'status' => 'pending']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/commissions/{$commission->id}")
            ->assertNotFound();
    }
}
