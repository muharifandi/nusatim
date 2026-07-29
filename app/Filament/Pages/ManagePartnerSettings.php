<?php

namespace App\Filament\Pages;

use App\Models\PartnerSetting;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
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
                Section::make('Withdrawal')
                    ->description('Bagian minimal dari Fase 23 (Partner Settings) - baru berisi yang dibutuhkan Fase 10 (Withdrawal). Field lain (default commission scheme, project claim rule, dst) menyusul.')
                    ->schema([
                        TextInput::make('minimum_withdrawal')
                            ->label('Minimum Penarikan')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Kosongkan untuk tidak ada batas minimum.'),
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
