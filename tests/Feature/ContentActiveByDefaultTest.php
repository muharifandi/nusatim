<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Faq;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Filament's Toggle field defaults to false unless ->default(true) is set
 * explicitly - these 7 resources' is_active toggle had no default, so
 * every piece of content created through the admin panel (a new FAQ,
 * team member, testimonial, etc.) was silently invisible on the live
 * site until someone noticed and manually flipped it on.
 */
class ContentActiveByDefaultTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Client/Project's FileUpload fields actually persist to disk
        // during Livewire form testing - fake the disk so these tests
        // don't litter the real public/media/uploads/ directory.
        Storage::fake('media');
    }

    public function test_new_client_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ClientResource\Pages\CreateClient::class)
            ->fillForm(['name' => 'Klien Baru', 'logo' => UploadedFile::fake()->image('logo.jpg')])
            ->call('create');

        $this->assertTrue(Client::where('name', 'Klien Baru')->firstOrFail()->is_active);
    }

    public function test_new_faq_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\FaqResource\Pages\CreateFaq::class)
            ->fillForm(['question' => 'Pertanyaan Baru?', 'answer' => 'Jawaban.'])
            ->call('create');

        $this->assertTrue(Faq::where('question', 'Pertanyaan Baru?')->firstOrFail()->is_active);
    }

    public function test_new_pricing_plan_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PricingPlanResource\Pages\CreatePricingPlan::class)
            ->fillForm([
                'name' => 'Paket Baru',
                'price' => 100000,
                'currency' => 'IDR',
                'cta_text' => 'Pilih Paket',
            ])
            ->call('create');

        $this->assertTrue(PricingPlan::where('name', 'Paket Baru')->firstOrFail()->is_active);
    }

    public function test_new_project_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ProjectResource\Pages\CreateProject::class)
            ->fillForm(['title' => 'Proyek Baru', 'slug' => 'proyek-baru', 'image' => UploadedFile::fake()->image('x.jpg')])
            ->call('create');

        $this->assertTrue(Project::where('slug', 'proyek-baru')->firstOrFail()->is_active);
    }

    public function test_new_service_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ServiceResource\Pages\CreateService::class)
            ->fillForm(['title' => 'Layanan Baru', 'slug' => 'layanan-baru', 'content' => 'Isi layanan.'])
            ->call('create');

        $this->assertTrue(Service::where('slug', 'layanan-baru')->firstOrFail()->is_active);
    }

    public function test_new_team_member_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\TeamMemberResource\Pages\CreateTeamMember::class)
            ->fillForm(['name' => 'Anggota Baru'])
            ->call('create');

        $this->assertTrue(TeamMember::where('name', 'Anggota Baru')->firstOrFail()->is_active);
    }

    public function test_new_testimonial_defaults_to_active(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\TestimonialResource\Pages\CreateTestimonial::class)
            ->fillForm(['name' => 'Pemberi Testimoni', 'quote' => 'Layanan bagus.', 'rating' => 5])
            ->call('create');

        $this->assertTrue(Testimonial::where('name', 'Pemberi Testimoni')->firstOrFail()->is_active);
    }
}
