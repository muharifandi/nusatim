<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingPlanResource\Pages;
use App\Filament\Resources\PricingPlanResource\RelationManagers;
use App\Models\PricingPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PricingPlanResource extends Resource
{
    protected static ?string $model = PricingPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('$'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(10)
                    ->default('IDR'),
                Forms\Components\TextInput::make('period')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TagsInput::make('features')
                    ->placeholder('Add a feature and press Enter')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cta_text')
                    ->required()
                    ->maxLength(255)
                    ->default('Choose Plan'),
                Forms\Components\TextInput::make('cta_url')
                    ->label('Tujuan Tombol (URL)')
                    ->helperText('Ke mana tombol "Choose Plan" mengarah, misalnya /contact atau https://wa.me/62xxxxxxxxxx. Kosongkan untuk memakai halaman Contact secara default.')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('is_highlighted')
                    ->label('Tandai Populer')
                    ->helperText('Menampilkan pita "Popular" di kartu paket ini.')
                    ->live()
                    ->required(),
                Forms\Components\ColorPicker::make('highlight_color')
                    ->label('Warna Highlight')
                    ->helperText('Warna pita "Popular". Kosongkan untuk memakai warna ungu default.')
                    ->visible(fn (Forms\Get $get) => $get('is_highlighted')),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cta_text')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cta_url')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_highlighted')
                    ->boolean(),
                Tables\Columns\ColorColumn::make('highlight_color'),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingPlans::route('/'),
            'create' => Pages\CreatePricingPlan::route('/create'),
            'edit' => Pages\EditPricingPlan::route('/{record}/edit'),
        ];
    }
}
