<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\TextInput::make('excerpt')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\RichEditor::make('content')
                    ->fileAttachmentsDisk('media')
                    ->fileAttachmentsDirectory('media/uploads')
                    ->fileAttachmentsVisibility('public')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('featured_image')
                    ->disk('media')
                    ->directory('media/uploads')
                    ->image(),
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options(fn () => \App\Models\Post::query()->whereNotNull('category')->distinct()->pluck('category', 'category'))
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('category')->required(),
                    ])
                    ->createOptionUsing(fn (array $data) => $data['category'])
                    ->default(null),
                Forms\Components\TagsInput::make('tags')
                    ->label('Keyword / Tag')
                    ->helperText('Tekan Enter setelah tiap keyword. Dipakai untuk filter pencarian di halaman blog.')
                    ->default(null),
                Forms\Components\TextInput::make('author_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\Toggle::make('is_published')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured (tampil paling atas di halaman blog)')
                    ->default(false),
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('meta_description')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\TextInput::make('meta_keywords')
                    ->helperText('Pisahkan dengan koma, contoh: teknologi, digital marketing, software')
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
                Tables\Columns\TextColumn::make('excerpt')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('featured_image')->disk('media'),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tags')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('author_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Total Baca')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-eye')
                    ->sortable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_keywords')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn () => Post::query()->whereNotNull('category')->distinct()->pluck('category', 'category')),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publish'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')->label('Dari tanggal'),
                        Forms\Components\DatePicker::make('published_until')->label('Sampai tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['published_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('published_at', '>=', $date))
                            ->when($data['published_until'] ?? null, fn (Builder $q, $date) => $q->whereDate('published_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators[] = 'Dari: '.\Carbon\Carbon::parse($data['published_from'])->format('d M Y');
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators[] = 'Sampai: '.\Carbon\Carbon::parse($data['published_until'])->format('d M Y');
                        }

                        return $indicators;
                    }),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
