<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerSalesTargetResource\Pages;
use App\Models\Partner;
use App\Models\PartnerSalesTarget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerSalesTargetResource extends Resource
{
    protected static ?string $model = PartnerSalesTarget::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Sales Target';

    protected static ?string $navigationGroup = 'Partner Program';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('partner_id')
                    ->label('Partner')
                    ->options(fn () => Partner::where('status', 'approved')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('period')
                    ->label('Periode (Bulan)')
                    ->helperText('Pilih tanggal berapa saja di bulan yang dimaksud - cuma bulan & tahunnya yang disimpan.')
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Carbon::parse($state)->startOfMonth()->toDateString() : $state),
                Forms\Components\TextInput::make('target_amount')
                    ->label('Target Nilai Penjualan')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->searchable(),
                Tables\Columns\TextColumn::make('period')->label('Periode')->date('F Y')->sortable(),
                Tables\Columns\TextColumn::make('target_amount')->label('Target')->money('IDR'),
            ])
            ->defaultSort('period', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePartnerSalesTargets::route('/'),
        ];
    }
}
