<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\CommissionResource\Pages;
use App\Models\Commission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $modelLabel = 'Commission';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Komisi';

    public static function canCreate(): bool
    {
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
                Forms\Components\TextInput::make('customer.name')->label('Customer')->disabled(),
                Forms\Components\TextInput::make('customer.service.title')->label('Produk')->disabled(),
                Forms\Components\TextInput::make('project_value')->label('Nilai Project')->disabled(),
                Forms\Components\TextInput::make('invoice_value')->label('Nilai Invoice')->disabled(),
                Forms\Components\TextInput::make('percentage')->label('Persentase')->disabled(),
                Forms\Components\TextInput::make('amount')->label('Nominal Komisi')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                Forms\Components\Textarea::make('rejection_reason')->label('Alasan Reject')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('customer.service.title')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('project_value')->label('Nilai Project')->money('IDR'),
                Tables\Columns\TextColumn::make('invoice_value')->label('Nilai Invoice')->money('IDR'),
                Tables\Columns\TextColumn::make('percentage')->label('Persentase')->suffix('%')->placeholder('-'),
                Tables\Columns\TextColumn::make('amount')->label('Nominal Komisi')->money('IDR'),
                Tables\Columns\IconColumn::make('is_bonus')->label('Bonus')->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        'waiting_client_payment' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissions::route('/'),
            'view' => Pages\ViewCommission::route('/{record}'),
        ];
    }
}
