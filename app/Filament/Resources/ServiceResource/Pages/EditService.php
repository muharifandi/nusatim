<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class EditService extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('checkSearchConsole')
                ->label('Cek di Search Console')
                ->icon('heroicon-o-magnifying-glass-circle')
                ->color('gray')
                ->visible(fn () => (bool) $this->record->is_active)
                ->action(function () {
                    $resourceId = SiteSetting::current()->search_console_resource_id;
                    $pageUrl = route('services.show', $this->record->slug);

                    if (blank($resourceId)) {
                        Notification::make()
                            ->title('Google Search Console Property belum diatur')
                            ->body('Isi dulu di Site Settings > Analytics, supaya tombol ini bisa langsung buka halaman inspeksi URL layanan ini.')
                            ->warning()
                            ->send();

                        $this->js('window.open('.json_encode('https://search.google.com/search-console').', "_blank")');

                        return;
                    }

                    $inspectUrl = 'https://search.google.com/search-console/inspect?'.http_build_query([
                        'resource_id' => $resourceId,
                        'id' => $pageUrl,
                    ]);

                    $this->js('window.open('.json_encode($inspectUrl).', "_blank")');
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
