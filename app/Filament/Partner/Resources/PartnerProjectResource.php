<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\PartnerProjectResource\Pages;
use App\Models\PartnerProject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PartnerProjectResource extends Resource
{
    protected static ?string $model = PartnerProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Project Board';

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * A mix of two things, not a simple partner-owned scope like Lead: any
     * project currently open for claiming (regardless of owner - there
     * isn't one yet), plus every project this partner has ever claimed,
     * whatever its current status.
     */
    public static function getEloquentQuery(): Builder
    {
        $partnerId = Auth::guard('partner')->id();

        return parent::getEloquentQuery()
            ->where(fn (Builder $q) => $q->where('status', 'available')->orWhere('partner_id', $partnerId));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\Textarea::make('description')->disabled(),
                Forms\Components\TextInput::make('budget')->disabled(),
                Forms\Components\TextInput::make('location')->disabled(),
                Forms\Components\TextInput::make('deadline')->disabled(),
                Forms\Components\TextInput::make('difficulty')->disabled(),
                Forms\Components\TextInput::make('commission_value')->label('Estimasi Komisi')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('service.title')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('budget')->money('IDR')->placeholder('-'),
                Tables\Columns\TextColumn::make('location')->placeholder('-'),
                Tables\Columns\TextColumn::make('deadline')->date()->placeholder('-'),
                Tables\Columns\TextColumn::make('difficulty')->placeholder('-'),
                Tables\Columns\TextColumn::make('commission_value')->label('Estimasi Komisi')->money('IDR')->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'available' => 'gray',
                        'pending_approval' => 'warning',
                        'assigned', 'in_progress' => 'info',
                        'closed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('claim')
                    ->label('Claim Project')
                    ->icon('heroicon-o-hand-raised')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (PartnerProject $record) => $record->status === 'available')
                    ->action(function (PartnerProject $record) {
                        $partner = Auth::guard('partner')->user();

                        try {
                            $claimed = $record->claim($partner);
                        } catch (ValidationException $exception) {
                            Notification::make()
                                ->title(collect($exception->errors())->flatten()->first())
                                ->danger()
                                ->send();

                            return;
                        }

                        if (! $claimed) {
                            Notification::make()
                                ->title('Project ini baru saja diklaim partner lain')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Klaim berhasil diajukan, menunggu persetujuan admin')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cancelClaim')
                    ->label('Batalkan Claim')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PartnerProject $record) => $record->status === 'pending_approval' && $record->partner_id === Auth::guard('partner')->id())
                    ->action(fn (PartnerProject $record) => $record->cancelClaim()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerProjects::route('/'),
            'view' => Pages\ViewPartnerProject::route('/{record}'),
        ];
    }
}
