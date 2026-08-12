<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * home.blade.php renders `rating` as that many <i class="fas fa-star">
 * icons in a for-loop with no upper bound - an out-of-range value (e.g.
 * 500, or 0/negative) either breaks the layout or renders no stars at
 * all instead of a valid 1-5 star testimonial.
 */
class TestimonialRatingBoundsTest extends TestCase
{
    use RefreshDatabase;

    public function test_rating_above_five_is_rejected(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\TestimonialResource\Pages\CreateTestimonial::class)
            ->fillForm(['name' => 'Klien', 'quote' => 'Bagus.', 'rating' => 500])
            ->call('create')
            ->assertHasFormErrors(['rating']);

        $this->assertFalse(Testimonial::where('name', 'Klien')->exists());
    }

    public function test_rating_below_one_is_rejected(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\TestimonialResource\Pages\CreateTestimonial::class)
            ->fillForm(['name' => 'Klien', 'quote' => 'Bagus.', 'rating' => 0])
            ->call('create')
            ->assertHasFormErrors(['rating']);

        $this->assertFalse(Testimonial::where('name', 'Klien')->exists());
    }

    public function test_rating_within_range_is_accepted(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\TestimonialResource\Pages\CreateTestimonial::class)
            ->fillForm(['name' => 'Klien', 'quote' => 'Bagus.', 'rating' => 4])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(Testimonial::where('name', 'Klien')->exists());
    }
}
