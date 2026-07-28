<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivity extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Aktivitas Halaman Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(PageView::query()->latest('viewed_at'))
            ->defaultPaginationPageOption(10)
            ->poll('30s')
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('Halaman')
                    ->searchable()
                    ->limit(40)
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('country_name')
                    ->label('Negara')
                    ->formatStateUsing(function (PageView $record) {
                        if (! $record->country_code || $record->country_code === 'XX') {
                            return '—';
                        }

                        return CountryMapWidget::flagEmoji($record->country_code).' '.$record->country_name;
                    }),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->copyable(),
                Tables\Columns\TextColumn::make('referrer')
                    ->label('Referrer')
                    ->limit(30)
                    ->placeholder('Langsung')
                    ->formatStateUsing(fn (?string $state) => $state ? parse_url($state, PHP_URL_HOST) : 'Langsung'),
                Tables\Columns\TextColumn::make('viewed_at')
                    ->label('Waktu')
                    ->since()
                    ->dateTimeTooltip('d M Y H:i:s'),
            ]);
    }
}
