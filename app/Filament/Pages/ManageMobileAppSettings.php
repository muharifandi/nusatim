<?php

namespace App\Filament\Pages;

use App\Models\MobileAppSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageMobileAppSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationLabel = 'Settings App Mobile';

    protected static ?string $title = 'Settings App Mobile';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 15;

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(MobileAppSetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Splash Screen')
                    ->description('Logo yang tampil di layar pembuka aplikasi mobile - terpisah dari logo website supaya bisa memakai varian yang lebih cocok untuk splash screen (mis. rasio persegi, tanpa teks tagline).')
                    ->schema([
                        FileUpload::make('splash_logo')
                            ->label('Logo Splash Screen')
                            ->disk('media')
                            ->directory('media/uploads')
                            ->image(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        MobileAppSetting::current()->update($data);

        Notification::make()
            ->title('Mobile app settings saved')
            ->success()
            ->send();
    }
}
