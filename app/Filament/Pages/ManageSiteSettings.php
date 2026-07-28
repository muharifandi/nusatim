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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSiteSettings extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
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
                    ]),

                Section::make('Performa & Tampilan')
                    ->schema([
                        Toggle::make('enable_image_skeleton')
                            ->label('Image Loading Placeholder')
                            ->helperText('Tampilkan animasi shimmer/skeleton pada gambar selagi belum selesai dimuat (dimatikan berarti gambar tampil polos seperti biasa).')
                            ->default(true),
                    ]),

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
