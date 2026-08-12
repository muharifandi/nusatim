<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Promotion::current() only ever reads the latest is_active=true row -
 * letting more than one promotion hold that flag simultaneously was
 * confusing: whoever activated the second one would see nothing change
 * on the public site, since the older one still "wins" by recency.
 */
class PromotionActiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_activating_a_promotion_deactivates_the_previously_active_one(): void
    {
        $promoA = Promotion::create(['title' => 'Promo A', 'image' => 'media/uploads/promo.jpg', 'is_active' => true]);
        $promoB = Promotion::create(['title' => 'Promo B', 'image' => 'media/uploads/promo2.jpg', 'is_active' => false]);

        $promoB->update(['is_active' => true]);

        $this->assertFalse($promoA->fresh()->is_active);
        $this->assertTrue($promoB->fresh()->is_active);
        $this->assertSame($promoB->id, Promotion::current()->id);
    }

    public function test_only_one_promotion_can_be_active_at_creation_time(): void
    {
        $promoA = Promotion::create(['title' => 'Promo A', 'image' => 'media/uploads/promo.jpg', 'is_active' => true]);
        $promoB = Promotion::create(['title' => 'Promo B', 'image' => 'media/uploads/promo2.jpg', 'is_active' => true]);

        $this->assertFalse($promoA->fresh()->is_active);
        $this->assertTrue($promoB->fresh()->is_active);
        $this->assertSame(1, Promotion::where('is_active', true)->count());
    }
}
