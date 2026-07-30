<?php

namespace App\Filament\Pages;

use App\Models\CommissionScheme;
use App\Models\PartnerSetting;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManagePartnerSettings extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Partner Settings';

    protected static ?string $navigationGroup = 'Reports & Settings';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.manage-partner-settings';

    public static function canAccess(): bool
    {
        return (bool) auth('web')->user()?->can('partner_setting.update');
    }

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
                    ->schema([
                        TextInput::make('minimum_withdrawal')
                            ->label('Minimum Penarikan')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Kosongkan untuk tidak ada batas minimum.'),
                    ]),
                Section::make('Commission Scheme Default')
                    ->description('Skema fallback kalau produk/partner/project tidak punya skema khusus. Urutan prioritas: Project → Partner → Produk → Default di sini → skema tanpa cakupan sama sekali (kalau ada).')
                    ->schema([
                        Select::make('default_commission_scheme_id')
                            ->label('Skema Default')
                            ->options(fn () => CommissionScheme::pluck('name', 'id'))
                            ->searchable(),
                    ]),
                Section::make('Project Claim Rule')
                    ->schema([
                        TextInput::make('max_concurrent_claimed_projects')
                            ->label('Maksimal Project Diklaim Bersamaan')
                            ->numeric()
                            ->helperText('Kosongkan untuk tidak ada batas.'),
                        TextInput::make('claim_processing_hours')
                            ->label('Batas Waktu Proses Klaim (jam)')
                            ->numeric()
                            ->helperText('Klaim yang masih Pending Approval melebihi jam ini otomatis ditolak (dibuka lagi untuk partner lain). Kosongkan untuk tidak ada batas waktu.'),
                    ])->columns(2),
                Section::make('Notifikasi')
                    ->description('Kanal default untuk partner yang baru mendaftar (partner tetap bisa ubah preferensinya sendiri di Fase 14). Isi/template teks tiap jenis notifikasi belum bisa diedit dari sini.')
                    ->schema([
                        Toggle::make('default_email_notifications_enabled')
                            ->label('Kirim email tambahan secara default untuk partner baru'),
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
