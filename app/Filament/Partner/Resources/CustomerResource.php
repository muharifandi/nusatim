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

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Customer';

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
                    ->options(fn () => Service::active()->pluck('name', 'id'))
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
                        Infolists\Components\TextEntry::make('service.name')->label('Produk')->placeholder('-'),
                        Infolists\Components\TextEntry::make('project_value')->label('Nilai Project')->money('IDR'),
                        Infolists\Components\TextEntry::make('payment_status')->label('Status Pembayaran')->badge(),
                    ])
                    ->columns(2),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('pic_name')->label('PIC')->placeholder('-'),
                Tables\Columns\TextColumn::make('service.name')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('project_value')->label('Nilai Project')->money('IDR'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
