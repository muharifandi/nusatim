<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/**
 * "Lead Monitoring" (Fase 17) - read-only cross-partner visibility,
 * ownership transfer, validation, and fuzzy anti-duplicate detection
 * (Lead::findPotentialDuplicates() - normalized phone, normalized email,
 * similar name).
 */
class LeadResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'lead';

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationLabel = 'Lead Monitoring';

    protected static ?string $navigationGroup = 'Partner Program';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('phone')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                Forms\Components\Placeholder::make('duplicates')
                    ->label('Kemungkinan Duplikat')
                    ->content(function (?Lead $record) {
                        if (! $record) {
                            return '-';
                        }

                        $matches = $record->findPotentialDuplicates();

                        if ($matches->isEmpty()) {
                            return new HtmlString('<span class="text-sm text-gray-500">Tidak ada lead lain yang mirip (telepon, email, atau nama).</span>');
                        }

                        return new HtmlString(
                            '<ul class="list-disc pl-4 text-sm">'
                            .$matches->map(fn (Lead $l) => '<li>'.e($l->name).' - '.e($l->partner->name).' ('.e($l->phone).')</li>')->implode('')
                            .'</ul>'
                        );
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Lead')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\IconColumn::make('is_validated')->label('Valid')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('validate')
                    ->label('Tandai Valid')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (Lead $record) => ! $record->is_validated)
                    ->action(fn (Lead $record) => $record->update(['is_validated' => true])),
                Tables\Actions\Action::make('transferOwnership')
                    ->label('Transfer Ownership')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->form([
                        Forms\Components\Select::make('partner_id')
                            ->label('Partner Tujuan')
                            ->options(fn (Lead $record) => Partner::where('id', '!=', $record->partner_id)->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn (Lead $record, array $data) => $record->update(['partner_id' => $data['partner_id']])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeads::route('/'),
        ];
    }
}
