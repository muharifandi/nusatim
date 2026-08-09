<?php

namespace Tests\Feature;

use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PageResourceServicesImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_about_images_on_services_page_does_not_wipe_other_content(): void
    {
        Storage::fake('media');

        $admin = User::factory()->create();

        $page = Page::create([
            'slug' => 'services',
            'name' => 'Layanan',
            'is_active' => true,
            'content' => [
                'heading' => 'Layanan Kami',
                'about_1_title' => 'Kolaborasi Erat dengan Tim Anda',
                'about_2_title' => 'Solusi yang Dibangun untuk Bertumbuh',
            ],
        ]);

        Livewire::actingAs($admin)
            ->test(EditPage::class, ['record' => $page->getKey()])
            ->fillForm([
                'about_1_image' => UploadedFile::fake()->image('about1.jpg'),
                'about_2_image' => UploadedFile::fake()->image('about2.jpg'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page->refresh();

        $this->assertNotEmpty($page->about_1_image);
        $this->assertNotEmpty($page->about_2_image);
        $this->assertSame('Layanan Kami', $page->content['heading']);
        $this->assertSame('Kolaborasi Erat dengan Tim Anda', $page->content['about_1_title']);
        $this->assertSame('Solusi yang Dibangun untuk Bertumbuh', $page->content['about_2_title']);
    }

    public function test_about_image_fields_only_show_on_services_page(): void
    {
        $admin = User::factory()->create();

        $servicesPage = Page::create(['slug' => 'services', 'name' => 'Layanan', 'is_active' => true]);
        $homePage = Page::create(['slug' => 'home', 'name' => 'Beranda', 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(EditPage::class, ['record' => $servicesPage->getKey()])
            ->assertFormFieldExists('about_1_image')
            ->assertFormFieldExists('about_2_image');

        Livewire::actingAs($admin)
            ->test(EditPage::class, ['record' => $homePage->getKey()])
            ->assertFormFieldDoesNotExist('about_1_image')
            ->assertFormFieldDoesNotExist('about_2_image');
    }
}
