<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 13;

    protected static ?string $modelLabel = 'Subscriber';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->disabled()
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('source')
                    ->disabled()
                    ->maxLength(255),
            ]);
    }

    public static function canCreate(): bool
    {
        // Subscriber hanya masuk lewat popup "Notify Us" di halaman publik.
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subscribed At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNewsletterSubscribers::route('/'),
        ];
    }
}
