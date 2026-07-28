<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentContactMessages extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Pesan Masuk Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactMessage::query()->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('gray')
                    ->falseColor('primary'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->limit(40)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->dateTimeTooltip('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ContactMessage $record): string => ContactMessageResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
