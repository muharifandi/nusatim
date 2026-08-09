<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalPageResource\Pages;
use App\Models\LegalPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LegalPageResource extends Resource
{
    protected static ?string $model = LegalPage::class;

    protected static ?string $modelLabel = 'Halaman Legal';

    protected static ?string $pluralModelLabel = 'Halaman Legal';

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dokumen')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Dipakai di URL, contoh: kebijakan-privasi → /legal/kebijakan-privasi'),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options(LegalPage::TYPES)
                            ->default('policy')
                            ->required()
                            ->live()
                            ->helperText('Kebijakan: tampilan artikel biasa (mis. Kebijakan Privasi, Syarat & Ketentuan). Dokumen Resmi: tampil dengan kop surat - logo, alamat, nomor surat (mis. Kontrak Kerja Sama).'),
                        Forms\Components\TextInput::make('document_number')
                            ->label('Nomor Surat')
                            ->maxLength(255)
                            ->placeholder('001/NSTM-KKS/VIII/2026')
                            ->visible(fn ($get) => $get('type') === 'document')
                            ->requiredIf('type', 'document'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Nonaktifkan untuk menyembunyikan halaman ini dari situs publik (404), tanpa menghapus datanya.')
                            ->default(true),
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Menentukan urutan tampil di daftar halaman legal & footer - angka kecil tampil lebih dulu.'),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Dokumen')
                            ->fileAttachmentsDisk('media')
                            ->fileAttachmentsDirectory('media/uploads')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Meta Data')
                    ->description('Untuk SEO - ditampilkan di hasil pencarian & saat dibagikan ke media sosial.')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('Kosongkan untuk memakai Judul di atas.'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(500),
                        Forms\Components\Textarea::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(500)
                            ->helperText('Pisahkan dengan koma.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => LegalPage::TYPES[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'document' ? 'warning' : 'gray'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalPages::route('/'),
            'create' => Pages\CreateLegalPage::route('/create'),
            'edit' => Pages\EditLegalPage::route('/{record}/edit'),
        ];
    }
}
