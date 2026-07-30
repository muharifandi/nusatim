<?php

namespace App\Filament\Partner\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Biodata')
                            ->schema([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                            ]),
                        Section::make('Foto Profil')
                            ->schema([
                                FileUpload::make('profile_photo_path')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->disk('partner_documents')
                                    ->directory('profile'),
                            ]),
                        Section::make('Rekening Bank')
                            ->schema([
                                TextInput::make('bank_name')->label('Nama Bank')->maxLength(255),
                                TextInput::make('bank_account_number')->label('Nomor Rekening')->maxLength(50),
                                TextInput::make('bank_account_holder')->label('Atas Nama')->maxLength(255),
                            ])
                            ->columns(3),
                        Section::make('Dokumen')
                            ->schema([
                                FileUpload::make('ktp_path')
                                    ->label('Foto KTP')
                                    ->image()
                                    ->disk('partner_documents')
                                    ->directory('profile')
                                    ->required(),
                                FileUpload::make('npwp_path')
                                    ->label('Foto NPWP (opsional)')
                                    ->image()
                                    ->disk('partner_documents')
                                    ->directory('profile'),
                            ]),
                        Section::make('Ganti Password')
                            ->description('Kosongkan kalau tidak ingin mengganti password.')
                            ->schema([
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ]),
                        Section::make('Preferensi Notifikasi')
                            ->schema([
                                Toggle::make('email_notifications_enabled')
                                    ->label('Kirim juga lewat email')
                                    ->helperText('Notifikasi in-app (lonceng di atas) selalu aktif dan tidak bisa dimatikan. Ini cuma mengatur email tambahan untuk beberapa notifikasi tertentu.'),
                            ]),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }
}
