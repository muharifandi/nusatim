<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The `order` field used to default to a hardcoded 0 on every one of
 * these resources - a new record always sorted to the very front (or
 * tied with any other record still at 0) instead of naturally appending
 * after whatever already existed.
 */
class OrderFieldDefaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_faq_appends_after_existing_ones_instead_of_defaulting_to_zero(): void
    {
        $admin = User::factory()->create();
        Faq::create(['question' => 'Q1', 'answer' => 'A1', 'order' => 5, 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\FaqResource\Pages\CreateFaq::class)
            ->assertFormSet(['order' => 6]);
    }

    public function test_new_faq_defaults_to_zero_when_none_exist_yet(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\FaqResource\Pages\CreateFaq::class)
            ->assertFormSet(['order' => 0]);
    }

    public function test_new_team_member_appends_after_existing_ones_instead_of_defaulting_to_zero(): void
    {
        $admin = User::factory()->create();
        TeamMember::create(['name' => 'Anggota 1', 'order' => 3, 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\TeamMemberResource\Pages\CreateTeamMember::class)
            ->assertFormSet(['order' => 4]);
    }
}
