<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Customer';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        // Customer selalu berasal dari Lead yang ditandai Won, bukan input manual.
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('partner_id', Auth::guard('partner')->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('pic_name')->label('Nama PIC')->maxLength(255),
                Forms\Components\TextInput::make('pic_phone')->label('Telepon PIC')->maxLength(50),
                Forms\Components\TextInput::make('pic_email')->label('Email PIC')->email()->maxLength(255),
                Forms\Components\Select::make('service_id')
                    ->label('Produk')
                    ->options(fn () => Service::active()->pluck('title', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('project_value')
                    ->label('Nilai Project')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid' => 'Belum Bayar',
                        'partial' => 'Sebagian',
                        'paid' => 'Lunas',
                    ])
                    ->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Customer')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('pic_name')->label('PIC')->placeholder('-'),
                        Infolists\Components\TextEntry::make('pic_phone')->label('Telepon PIC')->placeholder('-'),
                        Infolists\Components\TextEntry::make('pic_email')->label('Email PIC')->placeholder('-'),
                        Infolists\Components\TextEntry::make('service.title')->label('Produk')->placeholder('-'),
                        Infolists\Components\TextEntry::make('project_value')->label('Nilai Project')->money('IDR'),
                        Infolists\Components\TextEntry::make('payment_status')->label('Status Pembayaran')->badge(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Status Project')
                    ->visible(fn (Customer $record) => $record->partnerProject !== null)
                    ->schema([
                        Infolists\Components\TextEntry::make('partnerProject.name')->label('Nama Project'),
                        Infolists\Components\TextEntry::make('partnerProject.status')->label('Status Project')->badge(),
                        Infolists\Components\TextEntry::make('partnerProject.progress')->label('Progress')->suffix('%'),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Status Komisi')
                    ->visible(fn (Customer $record) => $record->commission !== null)
                    ->schema([
                        Infolists\Components\TextEntry::make('commission.status')->label('Status')->badge(),
                        Infolists\Components\TextEntry::make('commission.amount')->label('Nominal')->money('IDR'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Follow Up')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('follow_ups')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('remind_at')->label('Tanggal/Waktu')->dateTime(),
                                Infolists\Components\TextEntry::make('note')->placeholder('-'),
                                Infolists\Components\IconEntry::make('completed_at')->label('Selesai')->boolean(),
                            ])
                            ->columns(3),
                    ]),
                Infolists\Components\Section::make('Meeting')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('meetings')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('remind_at')->label('Tanggal/Waktu')->dateTime(),
                                Infolists\Components\TextEntry::make('note')->placeholder('-'),
                                Infolists\Components\IconEntry::make('completed_at')->label('Selesai')->boolean(),
                            ])
                            ->columns(3),
                    ]),
                Infolists\Components\Section::make('Proposal')
                    ->description('Dokumen yang diupload di Lead asal (belum ada entitas Proposal terpisah di sistem ini).')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('proposal_documents')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('name'),
                                Infolists\Components\TextEntry::make('url')
                                    ->label('')
                                    ->formatStateUsing(fn () => 'Lihat')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab(),
                            ])
                            ->columns(2),
                    ]),
                Infolists\Components\Section::make('Riwayat Aktivitas')
                    ->description('Gabungan timeline dari lead asal dan aktivitas setelah jadi customer.')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('timeline')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('type')->badge(),
                                Infolists\Components\TextEntry::make('body')->columnSpan(2),
                                Infolists\Components\TextEntry::make('created_at')->dateTime(),
                            ])
                            ->columns(4),
                    ]),
                Infolists\Components\Section::make('Aktivitas')
                    ->description('Event otomatis (dibuat, perubahan status, dokumen).')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('system_activities')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('type')->badge(),
                                Infolists\Components\TextEntry::make('body')->columnSpan(2),
                                Infolists\Components\TextEntry::make('created_at')->dateTime(),
                            ])
                            ->columns(4),
                    ]),
                Infolists\Components\Section::make('Catatan')
                    ->description('Catatan manual saja.')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('notes')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('body')->columnSpan(2),
                                Infolists\Components\TextEntry::make('created_at')->dateTime(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('pic_name')->label('PIC')->placeholder('-'),
                Tables\Columns\TextColumn::make('service.title')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('project_value')->label('Nilai Project')->money('IDR'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('partnerProject.name')->label('Nama Project')->placeholder('-'),
                Tables\Columns\TextColumn::make('partnerProject.status')->label('Status Project')->badge()->placeholder('-'),
                Tables\Columns\TextColumn::make('partnerProject.progress')->label('Progress')->suffix('%')->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('updateProgress')
                    ->label('Update Progress')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->visible(fn (Customer $record) => $record->partnerProject !== null)
                    ->form([
                        Forms\Components\TextInput::make('progress')
                            ->label('Progress')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->fillForm(fn (Customer $record) => ['progress' => $record->partnerProject?->progress])
                    ->action(fn (Customer $record, array $data) => $record->partnerProject->update(['progress' => $data['progress']])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
