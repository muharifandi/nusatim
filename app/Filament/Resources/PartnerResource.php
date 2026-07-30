<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Mail\PartnerRegistrationApproved;
use App\Mail\PartnerRegistrationRejected;
use App\Models\Partner;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\HtmlString;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Partner';

    protected static ?string $navigationGroup = 'Partner Program';

    public static function canCreate(): bool
    {
        // Partner mendaftar sendiri lewat /partner/register.
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                // Free text for now - "Level Partner" belum didefinisikan
                // secara final (lihat todo_partnert.md Fase 0), jadi
                // sementara admin isi bebas sampai levelnya dikonfirmasi.
                Forms\Components\TextInput::make('level')
                    ->label('Level Partner')
                    ->helperText('Belum ada daftar level baku - isi bebas untuk sementara.'),
                Forms\Components\TextInput::make('bank_name')->label('Nama Bank')->disabled(),
                Forms\Components\TextInput::make('bank_account_number')->label('Nomor Rekening')->disabled(),
                Forms\Components\TextInput::make('bank_account_holder')->label('Atas Nama')->disabled(),
                Forms\Components\Textarea::make('rejection_reason')->label('Alasan Reject')->disabled(),
                Forms\Components\Placeholder::make('documents')
                    ->label('Dokumen')
                    ->content(fn (?Partner $record) => $record ? new HtmlString(
                        collect([
                            'photo' => 'Foto Profil',
                            'ktp' => 'KTP',
                            'npwp' => 'NPWP',
                        ])->map(fn ($label, $type) => '<a class="underline" target="_blank" href="'.route('partner.documents.show', [$record, $type]).'">'.$label.'</a>')
                            ->implode(' &middot; ')
                    ) : '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'suspended' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('level')->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Mendaftar Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Partner $record) => $record->status !== 'approved')
                    ->action(function (Partner $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);

                        Mail::to($record->email)->send(
                            new PartnerRegistrationApproved($record, SiteSetting::current())
                        );
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Partner $record) => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Reject')
                            ->required(),
                    ])
                    ->action(function (Partner $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Mail::to($record->email)->send(
                            new PartnerRegistrationRejected($record, SiteSetting::current())
                        );
                    }),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->visible(fn (Partner $record) => $record->status === 'approved')
                    ->form([
                        // Reuses the same free-text `rejection_reason` column
                        // as the reject action - it's a generic "reason the
                        // account isn't in good standing" field, and a
                        // partner is never both rejected and suspended at
                        // once, so there's no ambiguity in storing either
                        // reason there.
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Suspend')
                            ->helperText('Opsional - ditampilkan ke partner di halaman status akun.'),
                    ])
                    ->action(fn (Partner $record, array $data) => $record->update([
                        'status' => 'suspended',
                        'rejection_reason' => $data['rejection_reason'] ?? null,
                    ])),
                Tables\Actions\Action::make('reactivate')
                    ->label('Aktifkan Kembali')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Partner $record) => $record->status === 'suspended')
                    ->action(fn (Partner $record) => $record->update([
                        'status' => 'approved',
                        'rejection_reason' => null,
                    ])),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->modalDescription('Partner akan menerima email berisi link untuk membuat password baru.')
                    ->visible(fn (Partner $record) => $record->status !== 'pending_review')
                    ->action(function (Partner $record) {
                        // Reuses the exact same reset-link mechanism already
                        // wired up for the partner-facing "Lupa Password"
                        // flow (Fase 1, Filament's passwordReset() +
                        // the 'partners' broker) - admin just triggers it on
                        // the partner's behalf instead of building a
                        // separate "set new password directly" flow.
                        $status = Password::broker('partners')->sendResetLink(['email' => $record->email]);

                        Notification::make()
                            ->title($status === Password::RESET_LINK_SENT
                                ? 'Link reset password terkirim ke '.$record->email
                                : 'Gagal mengirim link reset password')
                            ->color($status === Password::RESET_LINK_SENT ? 'success' : 'danger')
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePartners::route('/'),
        ];
    }
}
