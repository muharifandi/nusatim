<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\FileUpload::make('image')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image(),
                Forms\Components\TextInput::make('short_description')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\RichEditor::make('content')
                    ->fileAttachmentsDisk('media')
                    ->fileAttachmentsDirectory('media/uploads')
                    ->fileAttachmentsVisibility('public')
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('features')
                    ->label('Badge Fitur (halaman detail service)')
                    ->helperText('4 badge yang tampil di halaman detail service ini. Kosongkan semua untuk memakai badge generik bawaan.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon (kelas flaticon)')
                            ->helperText('Contoh: flaticon-responsive')
                            ->maxLength(50),
                        Forms\Components\Select::make('color')
                            ->label('Warna')
                            ->options([
                                'dodger-blue' => 'Dodger Blue',
                                'sunset-orange' => 'Sunset Orange',
                                'royal-blue' => 'Royal Blue',
                                'california' => 'California',
                                'emerald' => 'Emerald',
                                'turquoise' => 'Turquoise',
                            ])
                            ->default('dodger-blue'),
                    ])
                    ->columns(3)
                    ->maxItems(4)
                    ->defaultItems(0)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('meta_description')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\FileUpload::make('og_image')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')->disk('media'),
                Tables\Columns\TextColumn::make('short_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_description')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('og_image')->disk('media'),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
