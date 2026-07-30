<?php

namespace App\Filament\Partner\Pages\Auth;

use App\Mail\PartnerRegistrationReceived;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\SiteSetting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
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
                        Wizard::make([
                            Step::make('Akun')
                                ->schema([
                                    $this->getNameFormComponent(),
                                    $this->getEmailFormComponent(),
                                    $this->getPasswordFormComponent(),
                                    $this->getPasswordConfirmationFormComponent(),
                                ]),
                            Step::make('Dokumen')
                                ->schema([
                                    FileUpload::make('profile_photo_path')
                                        ->label('Foto Profil')
                                        ->image()
                                        ->disk('partner_documents')
                                        ->directory('registrations')
                                        ->required(),
                                    FileUpload::make('ktp_path')
                                        ->label('Foto KTP')
                                        ->image()
                                        ->disk('partner_documents')
                                        ->directory('registrations')
                                        ->required(),
                                    FileUpload::make('npwp_path')
                                        ->label('Foto NPWP (opsional)')
                                        ->image()
                                        ->disk('partner_documents')
                                        ->directory('registrations'),
                                ]),
                            Step::make('Rekening Bank')
                                ->schema([
                                    TextInput::make('bank_name')
                                        ->label('Nama Bank')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('bank_account_number')
                                        ->label('Nomor Rekening')
                                        ->required()
                                        ->maxLength(50),
                                    TextInput::make('bank_account_holder')
                                        ->label('Atas Nama')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                            Step::make('Perjanjian')
                                ->schema([
                                    Placeholder::make('agreement_text')
                                        ->label('Perjanjian Kemitraan')
                                        ->content(fn () => new HtmlString(
                                            '<div class="prose prose-sm dark:prose-invert max-h-64 overflow-y-auto rounded-lg border p-4">'
                                            .(PartnerSetting::current()->partnership_agreement ?: '<em>Perjanjian belum diisi oleh admin.</em>')
                                            .'</div>'
                                        )),
                                    Checkbox::make('agreement')
                                        ->label('Saya sudah membaca dan menyetujui perjanjian kemitraan di atas')
                                        ->accepted()
                                        ->required()
                                        ->dehydrated(false),
                                ]),
                        ]),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function handleRegistration(array $data): Model
    {
        $partner = Partner::create([
            ...$data,
            'status' => 'pending_review',
            'agreement_accepted_at' => now(),
            'email_notifications_enabled' => PartnerSetting::current()->default_email_notifications_enabled,
        ]);

        Mail::to($partner->email)->send(
            new PartnerRegistrationReceived($partner, SiteSetting::current())
        );

        return $partner;
    }
}
