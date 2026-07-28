<?php

namespace App\Filament\Pages;

use App\Models\PartnerSetting;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManagePartnerSettings extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Partner Settings';

    protected static ?string $navigationGroup = 'Partner Program';

    protected static string $view = 'filament.pages.manage-partner-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PartnerSetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Perjanjian Kemitraan')
                    ->description('Teks ini ditampilkan ke calon partner di halaman registrasi (/partner/register), sebelum mereka mencentang persetujuan.')
                    ->schema([
                        RichEditor::make('partnership_agreement')
                            ->label('')
                            ->hiddenLabel(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        PartnerSetting::current()->update($data);

        Notification::make()
            ->title('Partner settings saved')
            ->success()
            ->send();
    }
}
