<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $modelLabel = 'Page';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Nonaktifkan untuk membuat halaman ini 404 di situs publik, tanpa menghapus datanya.')
                    ->default(true),
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('meta_description')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\TextInput::make('meta_keywords')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\FileUpload::make('og_image')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image(),
                Forms\Components\FileUpload::make('about_1_image')
                    ->label('Gambar "Kolaborasi Erat dengan Tim Anda"')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image()
                    ->visible(fn (?Page $record) => $record?->slug === 'services')
                    ->helperText('Khusus halaman Layanan - section about pertama. Kosongkan untuk pakai gambar bawaan.'),
                Forms\Components\FileUpload::make('about_2_image')
                    ->label('Gambar "Solusi yang Dibangun untuk Bertumbuh"')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image()
                    ->visible(fn (?Page $record) => $record?->slug === 'services')
                    ->helperText('Khusus halaman Layanan - section about kedua. Kosongkan untuk pakai gambar bawaan.'),
                Forms\Components\KeyValue::make('content')
                    ->keyLabel('Field')
                    ->valueLabel('Text')
                    ->reorderable()
                    ->columnSpanFull()
                    ->helperText('Semua judul/label/teks section untuk halaman ini. Contoh key untuk Home: hero_title, hero_subtitle, hero_text, about_preview_title, dst. Untuk halaman Layanan, gambar about_1_image/about_2_image dan tombol "Selengkapnya" diatur lewat field khusus di bawah, bukan di sini. Teks/URL tombol "Selengkapnya" pada card layanan diatur per-layanan di menu Website > Services (setiap layanan tombolnya beda tujuan).'),
                Forms\Components\Section::make('Tombol "Selengkapnya" section About (khusus halaman Layanan)')
                    ->visible(fn (?Page $record) => $record?->slug === 'services')
                    ->schema([
                        Forms\Components\TextInput::make('content.about_1_button_text')
                            ->label('Teks tombol - "Kolaborasi Erat dengan Tim Anda"')
                            ->placeholder('Selengkapnya')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('content.about_1_button_url')
                            ->label('URL tombol - "Kolaborasi Erat dengan Tim Anda"')
                            ->placeholder('/about')
                            ->helperText('Kosongkan untuk mengarah ke halaman About.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('content.about_2_button_text')
                            ->label('Teks tombol - "Solusi yang Dibangun untuk Bertumbuh"')
                            ->placeholder('Selengkapnya')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('content.about_2_button_url')
                            ->label('URL tombol - "Solusi yang Dibangun untuk Bertumbuh"')
                            ->placeholder('/about')
                            ->helperText('Kosongkan untuk mengarah ke halaman About.')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_keywords')
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
