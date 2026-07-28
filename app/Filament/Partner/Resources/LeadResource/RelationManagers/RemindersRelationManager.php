<?php

namespace App\Filament\Partner\Resources\LeadResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RemindersRelationManager extends RelationManager
{
    protected static string $relationship = 'reminders';

    protected static ?string $title = 'Reminder Follow Up & Meeting';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'follow_up' => 'Follow Up',
                        'meeting' => 'Meeting',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('remind_at')
                    ->label('Tanggal/Waktu')
                    ->required(),
                Forms\Components\Textarea::make('note'),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Selesai Pada')
                    ->helperText('Isi kalau reminder ini sudah ditindaklanjuti.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'meeting' ? 'Meeting' : 'Follow Up'),
                Tables\Columns\TextColumn::make('remind_at')->label('Tanggal/Waktu')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('note')->limit(40),
                Tables\Columns\IconColumn::make('completed_at')->label('Selesai')->boolean(),
            ])
            ->defaultSort('remind_at')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
