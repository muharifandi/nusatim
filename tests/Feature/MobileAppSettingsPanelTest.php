<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageMobileAppSettings;
use App\Models\MobileAppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MobileAppSettingsPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_save_mobile_app_settings(): void
    {
        Storage::fake('media');

        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        $this->get('/admin/manage-mobile-app-settings')->assertOk();

        Livewire::test(ManageMobileAppSettings::class)
            ->fillForm([
                'splash_logo' => UploadedFile::fake()->image('splash.png'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = MobileAppSetting::current();
        $this->assertNotNull($settings->splash_logo);
        Storage::disk('media')->assertExists($settings->splash_logo);
    }
}
