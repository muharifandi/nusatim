<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Item')
                    ->options(fn () => $this->getOwnerRecord()->allItems()->pluck('label', 'id'))
                    ->searchable()
                    ->helperText('Kosongkan untuk item level atas (top-level menu).'),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->maxLength(255)
                    ->helperText('Kosongkan kalau item ini hanya trigger dropdown/mega-menu.'),
                Forms\Components\Select::make('type')
                    ->options([
                        'link' => 'Link biasa',
                        'dropdown' => 'Dropdown (punya sub-menu)',
                        'mega_menu' => 'Mega Menu (grid gambar, top-level saja)',
                    ])
                    ->default('link')
                    ->required(),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->helperText('Class icon flaticon, contoh: flaticon-shout'),
                Forms\Components\FileUpload::make('image')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image()
                    ->helperText('Dipakai untuk item di dalam Mega Menu.'),
                Forms\Components\TextInput::make('target')
                    ->maxLength(20)
                    ->default('_self'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable(),
                Tables\Columns\TextColumn::make('parent.label')->label('Parent')->default('-'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('url')->limit(30),
                Tables\Columns\TextColumn::make('order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('order')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
