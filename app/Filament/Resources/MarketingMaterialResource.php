<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketingMaterialResource\Pages;
use App\Models\MarketingMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketingMaterialResource extends Resource
{
    protected static ?string $model = MarketingMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Marketing Material';

    protected static ?string $navigationGroup = 'Partner Program';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Select::make('category')
                    ->options(MarketingMaterial::CATEGORIES)
                    ->live()
                    ->required(),
                Forms\Components\Textarea::make('description')->label('Deskripsi Singkat'),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->visible(fn (Forms\Get $get) => in_array($get('category'), MarketingMaterial::FILE_CATEGORIES))
                    ->required(fn (Forms\Get $get) => in_array($get('category'), MarketingMaterial::FILE_CATEGORIES)),
                Forms\Components\RichEditor::make('content')
                    ->label('Isi')
                    ->visible(fn (Forms\Get $get) => filled($get('category')) && ! in_array($get('category'), MarketingMaterial::FILE_CATEGORIES))
                    ->required(fn (Forms\Get $get) => filled($get('category')) && ! in_array($get('category'), MarketingMaterial::FILE_CATEGORIES)),
                Forms\Components\TextInput::make('order')->numeric()->default(0)->required(),
                Forms\Components\Toggle::make('is_active')->default(true)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => MarketingMaterial::CATEGORIES[$state] ?? $state),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->defaultSort('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMarketingMaterials::route('/'),
        ];
    }
}
