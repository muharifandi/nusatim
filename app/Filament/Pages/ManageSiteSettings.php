<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

class ManageSiteSettings extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 14;

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $data = SiteSetting::current()->toArray();

        // Never send the decrypted SMTP password to the browser - it would
        // sit in the Livewire component's public state (visible in the page
        // source/network tab) even though the input itself renders masked.
        // Leaving it blank means "no change" on save (see the password
        // field's dehydrated() below).
        unset($data['mail_password']);

        $this->form->fill($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testEmail')
                ->label('Test Kirim Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                // Tests against the SAVED settings in the database, not
                // whatever's currently typed in the unsaved form - keeps the
                // action simple and avoids sending mail using a half-edited,
                // not-yet-persisted password. Save first, then test.
                ->modalDescription('Mengirim email test memakai pengaturan SMTP yang TERSIMPAN saat ini. Simpan perubahan dulu sebelum test kalau baru saja mengubah pengaturan di bawah.')
                ->form([
                    TextInput::make('test_recipient')
                        ->label('Kirim ke email')
                        ->email()
                        ->required()
                        ->default(fn () => SiteSetting::current()->email),
                ])
                ->action(function (array $data): void {
                    $settings = SiteSetting::current();
                    $settings->applyMailConfig();

                    try {
                        Mail::raw(
                            'Ini email test dari pengaturan SMTP di Site Settings. Jika Anda menerima email ini, konfigurasi SMTP sudah benar.',
                            fn ($message) => $message->to($data['test_recipient'])->subject('Test Email - Pengaturan SMTP')
                        );

                        Notification::make()
                            ->title('Email test berhasil dikirim')
                            ->body("Terkirim ke {$data['test_recipient']}. Cek inbox (dan folder Spam).")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Gagal mengirim email test')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
            Action::make('previewComingSoon')
                ->label('Preview Coming Soon')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(route('coming-soon'))
                ->openUrlInNewTab(),
            Action::make('exportSitemap')
                ->label('Export Sitemap.xml')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('sitemap'))
                ->openUrlInNewTab(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Company Info')
                    ->schema([
                        TextInput::make('company_name')->required(),
                        TextInput::make('legal_name'),
                        TextInput::make('tagline'),
                        TextInput::make('email')->email(),
                        TextInput::make('phone'),
                        TextInput::make('address'),
                        TextInput::make('google_maps_embed_url')
                            ->label('Google Maps Embed URL')
                            ->helperText('Buka Google Maps > cari lokasi > Share > Embed a map > salin URL dari atribut src iframe. Kosongkan untuk memakai pencarian otomatis berdasarkan alamat di atas.')
                            ->url(),
                    ])->columns(2),

                Section::make('Logo & Favicon')
                    ->schema([
                        FileUpload::make('logo_light')->disk('media')->directory('media/uploads')->image(),
                        FileUpload::make('logo_dark')->disk('media')->directory('media/uploads')->image(),
                        FileUpload::make('logo_mobile')
                            ->label('Logo Mobile')
                            ->helperText('Logo yang tampil di menu navigasi saat dibuka di layar HP/tablet.')
                            ->disk('media')->directory('media/uploads')->image(),
                        FileUpload::make('logo_footer')
                            ->label('Logo Footer')
                            ->helperText('Logo yang tampil di bagian footer. Kosongkan untuk memakai Logo Light secara default.')
                            ->disk('media')->directory('media/uploads')->image(),
                        FileUpload::make('favicon')->disk('media')->directory('media/uploads')->image(),
                        FileUpload::make('preloader_logo')
                            ->label('Logo Loading (Preloader)')
                            ->helperText('Logo yang tampil di tengah animasi loading saat halaman pertama kali dibuka/reload. Kosongkan untuk memakai logo bawaan.')
                            ->disk('media')->directory('media/uploads')->image(),
                    ])->columns(2),

                Section::make('Navigasi')
                    ->schema([
                        TextInput::make('nav_cta_text')
                            ->label('Teks Tombol CTA (Menu Navigasi)')
                            ->placeholder('Get a Quote')
                            ->maxLength(50),
                        Toggle::make('show_language_switcher')
                            ->label('Tampilkan Tombol Bahasa')
                            ->helperText('Tombol pilihan bahasa (ID/EN) di menu navigasi. Nonaktifkan kalau versi bahasa Inggris situs belum tersedia - saat ini opsi English masih ditandai "Segera Hadir" dan tidak berfungsi.')
                            ->default(false),
                    ]),

                Section::make('Halaman Layanan (Services)')
                    ->description('Konten yang tampil di panel "Explore Our Other Services" pada setiap halaman detail layanan.')
                    ->schema([
                        TextInput::make('services_explore_heading')
                            ->label('Judul Panel')
                            ->placeholder('Explore Our Other Services')
                            ->maxLength(100),
                        FileUpload::make('services_explore_image')
                            ->label('Gambar Panel')
                            ->disk('media')->directory('media/uploads')->image(),
                    ])->columns(2),

                Section::make('Social Media')
                    ->schema([
                        TextInput::make('facebook_url')->url(),
                        TextInput::make('twitter_url')->url(),
                        TextInput::make('instagram_url')->url(),
                        TextInput::make('linkedin_url')->url(),
                        TextInput::make('youtube_url')->url(),
                    ])->columns(2),

                Section::make('Default SEO')
                    ->schema([
                        TextInput::make('default_meta_title'),
                        Textarea::make('default_meta_description'),
                        Textarea::make('default_meta_keywords'),
                        FileUpload::make('default_og_image')->disk('media')->directory('media/uploads')->image(),
                    ]),

                Section::make('Analytics')
                    ->description('Masukkan Measurement ID Google Analytics (format G-XXXXXXXXXX) untuk mengaktifkan pelacakan pengunjung. Kosongkan untuk menonaktifkan.')
                    ->schema([
                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics Measurement ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->maxLength(50),
                        TextInput::make('search_console_resource_id')
                            ->label('Google Search Console Property')
                            ->placeholder('sc-domain:nusatim.com atau https://nusatim.com/')
                            ->helperText('Dari Search Console: pilih property Anda, lihat di URL address bar setelah "resource_id=". Dipakai untuk tombol "Cek di Search Console" di halaman edit artikel Blog.')
                            ->maxLength(255),
                    ]),

                Section::make('Performa & Tampilan')
                    ->schema([
                        Toggle::make('enable_image_skeleton')
                            ->label('Image Loading Placeholder')
                            ->helperText('Tampilkan animasi shimmer/skeleton pada gambar selagi belum selesai dimuat (dimatikan berarti gambar tampil polos seperti biasa).')
                            ->default(true),
                    ]),

                Section::make('Pengaturan Email (SMTP)')
                    ->description('Kelola kredensial SMTP dari sini, tanpa perlu akses file/SSH ke server. Kalau dimatikan, aplikasi memakai pengaturan mail bawaan dari file .env di server.')
                    ->schema([
                        Toggle::make('mail_use_custom_smtp')
                            ->label('Gunakan SMTP Kustom')
                            ->helperText('Aktifkan untuk menimpa pengaturan .env dengan kredensial di bawah ini.')
                            ->live()
                            ->default(false),
                        TextInput::make('mail_host')
                            ->label('SMTP Host')
                            ->placeholder('mail.nusatim.com')
                            ->visible(fn ($get) => $get('mail_use_custom_smtp'))
                            ->requiredIf('mail_use_custom_smtp', true),
                        TextInput::make('mail_port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->placeholder('465')
                            ->visible(fn ($get) => $get('mail_use_custom_smtp'))
                            ->requiredIf('mail_use_custom_smtp', true),
                        Select::make('mail_encryption')
                            ->label('Enkripsi')
                            ->options([
                                '' => 'Tidak Ada',
                                'smtps' => 'SSL/TLS (biasanya port 465)',
                                'smtp' => 'STARTTLS (biasanya port 587)',
                            ])
                            ->default('smtps')
                            ->visible(fn ($get) => $get('mail_use_custom_smtp')),
                        TextInput::make('mail_username')
                            ->label('SMTP Username')
                            ->placeholder('info@nusatim.com')
                            ->visible(fn ($get) => $get('mail_use_custom_smtp'))
                            ->requiredIf('mail_use_custom_smtp', true),
                        TextInput::make('mail_password')
                            ->label('SMTP Password')
                            ->password()
                            ->revealable()
                            ->placeholder('Kosongkan supaya password tersimpan tidak berubah')
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn ($get) => $get('mail_use_custom_smtp')),
                        TextInput::make('mail_from_address')
                            ->label('Alamat Pengirim (From)')
                            ->email()
                            ->placeholder('info@nusatim.com')
                            ->helperText('Kosongkan untuk memakai SMTP Username di atas.')
                            ->visible(fn ($get) => $get('mail_use_custom_smtp')),
                        TextInput::make('mail_from_name')
                            ->label('Nama Pengirim (From)')
                            ->placeholder('Nusatim')
                            ->helperText('Kosongkan untuk memakai Company Name.')
                            ->visible(fn ($get) => $get('mail_use_custom_smtp')),
                    ])->columns(2),

                Section::make('Mode Coming Soon')
                    ->description('Saat aktif, seluruh halaman publik akan menampilkan halaman "Coming Soon" (kecuali halaman Contact dan admin panel). Isi/teks halaman ini bisa diedit di menu Pages dengan slug "coming-soon".')
                    ->schema([
                        Toggle::make('coming_soon_enabled')
                            ->label('Aktifkan Mode Coming Soon')
                            ->helperText('Gunakan saat website sedang dalam perbaikan besar atau belum siap diluncurkan ke publik.')
                            ->default(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update($data);

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }
}
