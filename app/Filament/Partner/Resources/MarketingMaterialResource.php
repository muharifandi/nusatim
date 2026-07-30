<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\MarketingMaterialResource\Pages;
use App\Models\MarketingMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MarketingMaterialResource extends Resource
{
    protected static ?string $model = MarketingMaterial::class;

    protected static ?string $modelLabel = 'Marketing Material';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Marketing Center';

    protected static ?int $navigationSort = 8;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->active()->orderBy('order');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->disabled(),
                Forms\Components\Textarea::make('description')->disabled(),
                Forms\Components\Placeholder::make('file_path')
                    ->label('File')
                    ->visible(fn (?MarketingMaterial $record) => $record?->isFileBased())
                    ->content(fn (?MarketingMaterial $record) => $record?->file_path
                        ? new \Illuminate\Support\HtmlString('<a class="underline" target="_blank" href="'.asset($record->file_path).'">Download</a>')
                        : '-'),
                Forms\Components\RichEditor::make('content')
                    ->label('Isi')
                    ->disabled()
                    ->visible(fn (?MarketingMaterial $record) => $record && ! $record->isFileBased()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(60)->placeholder('-'),
            ])
            ->defaultGroup('category')
            ->groups([
                Tables\Grouping\Group::make('category')
                    ->label('Kategori')
                    ->getTitleFromRecordUsing(fn (MarketingMaterial $record) => MarketingMaterial::CATEGORIES[$record->category] ?? $record->category),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (MarketingMaterial $record) => $record->isFileBased() && $record->file_path)
                    ->url(fn (MarketingMaterial $record) => asset($record->file_path))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarketingMaterials::route('/'),
            'view' => Pages\ViewMarketingMaterial::route('/{record}'),
        ];
    }
}
