<?php

namespace App\Filament\Partner\Resources\LeadResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Read-only activity timeline (auto-logged by Lead's model events on
 * create/status change) - the only manual write allowed here is adding a
 * free-form internal note, everything else is generated automatically.
 */
class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Timeline & Catatan Internal';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'created' => 'Dibuat',
                        'status_change' => 'Perubahan Status',
                        'document' => 'Dokumen',
                        default => 'Catatan',
                    }),
                Tables\Columns\TextColumn::make('body')->wrap(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Catatan')
                    ->form([
                        Forms\Components\Textarea::make('body')
                            ->label('Catatan')
                            ->required(),
                    ])
                    ->using(function (array $data, RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->activities()->create([
                            'type' => 'note',
                            'body' => $data['body'],
                        ]);
                    }),
            ])
            ->actions([]);
    }
}
