<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class EditPost extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('checkSearchConsole')
                ->label('Cek di Search Console')
                ->icon('heroicon-o-magnifying-glass-circle')
                ->color('gray')
                ->visible(fn () => $this->record->isLive())
                ->action(function () {
                    $resourceId = SiteSetting::current()->search_console_resource_id;
                    $pageUrl = route('blog.show', $this->record->slug);

                    if (blank($resourceId)) {
                        Notification::make()
                            ->title('Google Search Console Property belum diatur')
                            ->body('Isi dulu di Site Settings > Analytics, supaya tombol ini bisa langsung buka halaman inspeksi URL artikel ini.')
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
