<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Withdrawal';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('partner_id', Auth::guard('partner')->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('available_balance')
                    ->label('Saldo Tersedia')
                    ->content(fn () => 'Rp'.number_format(Auth::guard('partner')->user()->availableBalance(), 0, ',', '.'))
                    ->visibleOn('create'),
                Forms\Components\TextInput::make('amount')
                    ->label('Nominal Penarikan')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->disabledOn('view'),
                Forms\Components\FileUpload::make('ktp_path')
                    ->label('Foto KTP')
                    ->image()
                    ->disk('partner_documents')
                    ->directory('withdrawals')
                    ->required()
                    ->visibleOn('create'),
                Forms\Components\Textarea::make('note')
                    ->label('Catatan (opsional)')
                    ->disabledOn('view'),
                Forms\Components\TextInput::make('bank_name')->disabledOn('view')->visibleOn('view'),
                Forms\Components\TextInput::make('bank_account_number')->disabledOn('view')->visibleOn('view'),
                Forms\Components\TextInput::make('bank_account_holder')->disabledOn('view')->visibleOn('view'),
                Forms\Components\TextInput::make('status')->disabledOn('view')->visibleOn('view'),
                Forms\Components\Textarea::make('rejection_reason')->label('Alasan Reject')->disabledOn('view')->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')->label('Nominal')->money('IDR'),
                Tables\Columns\TextColumn::make('bank_name')->label('Bank'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
            'view' => Pages\ViewWithdrawal::route('/{record}'),
        ];
    }
}
