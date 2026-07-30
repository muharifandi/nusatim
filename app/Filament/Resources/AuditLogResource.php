<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Fase 27 - read-only. Rows are only ever written automatically by
 * LogsAudit (see app/Models/Concerns/LogsAudit.php), never created/edited/
 * deleted through the UI - an audit trail that could be edited wouldn't be
 * trustworthy as one.
 */
class AuditLogResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'audit_log';

    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $navigationGroup = 'RBAC & Sistem';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Audit Log')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')->label('Waktu')->dateTime(),
                        Infolists\Components\TextEntry::make('auditable_type')->label('Model')->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-'),
                        Infolists\Components\TextEntry::make('auditable_id')->label('ID')->placeholder('-'),
                        Infolists\Components\TextEntry::make('action')->badge(),
                        Infolists\Components\TextEntry::make('user_type')
                            ->label('Aktor')
                            ->formatStateUsing(function (AuditLog $record) {
                                if (! $record->user_type) {
                                    return 'Sistem';
                                }

                                return class_basename($record->user_type).': '.($record->user?->name ?? "#{$record->user_id}");
                            }),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Perubahan')
                    ->visible(fn (AuditLog $record) => filled($record->changes))
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('changes.before')->label('Sebelum'),
                        Infolists\Components\KeyValueEntry::make('changes.after')->label('Sesudah'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('auditable_id')->label('ID')->placeholder('-'),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('user_type')
                    ->label('Aktor')
                    ->formatStateUsing(function (AuditLog $record) {
                        if (! $record->user_type) {
                            return 'Sistem';
                        }

                        return class_basename($record->user_type).': '.($record->user?->name ?? "#{$record->user_id}");
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Model')
                    ->options(fn () => AuditLog::query()
                        ->whereNotNull('auditable_type')
                        ->distinct()
                        ->pluck('auditable_type', 'auditable_type')
                        ->mapWithKeys(fn ($value, $key) => [$key => class_basename($key)])),
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
