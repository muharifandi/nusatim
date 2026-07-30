<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\CommissionSchemeResource\Pages;
use App\Models\CommissionScheme;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommissionSchemeResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'commission_scheme';

    protected static ?string $model = CommissionScheme::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Commission Scheme';

    protected static ?string $navigationGroup = 'Commission & Withdrawal';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Jenis Skema')
                    ->options([
                        'percentage' => 'Percentage',
                        'recurring_percentage' => 'Recurring Percentage',
                        'flat' => 'Flat Commission',
                    ])
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('percentage')
                    ->label('Persentase (%)')
                    ->numeric()
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['percentage', 'recurring_percentage']))
                    ->required(fn (Forms\Get $get) => in_array($get('type'), ['percentage', 'recurring_percentage'])),
                Forms\Components\TextInput::make('flat_amount')
                    ->label('Nominal Flat')
                    ->numeric()
                    ->prefix('Rp')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'flat')
                    ->required(fn (Forms\Get $get) => $get('type') === 'flat'),

                Forms\Components\Section::make('Cakupan Skema')
                    ->description('Isi salah satu saja (Produk / Partner / Project). Kosongkan semua untuk jadikan skema global (fallback kalau tidak ada yang lebih spesifik cocok). Urutan prioritas: Project > Partner > Produk > Default.')
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->label('Per Produk')
                            ->options(fn () => Service::active()->pluck('title', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('partner_id')
                            ->label('Per Partner')
                            ->options(fn () => Partner::where('status', 'approved')->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('partner_project_id')
                            ->label('Per Project')
                            ->options(fn () => PartnerProject::pluck('name', 'id'))
                            ->searchable(),
                    ])->columns(3),

                Forms\Components\DatePicker::make('starts_at')->label('Berlaku Mulai'),
                Forms\Components\DatePicker::make('ends_at')->label('Berlaku Sampai'),
                Forms\Components\Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'percentage' => 'Percentage',
                        'recurring_percentage' => 'Recurring Percentage',
                        'flat' => 'Flat',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('service.title')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->placeholder('-'),
                Tables\Columns\TextColumn::make('partnerProject.name')->label('Project')->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommissionSchemes::route('/'),
        ];
    }
}
