<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Fase 26 (new module, not in the original spec - added because the
 * client's "Final Review" names it as one of 6 workflows needing
 * Assignment). Partner can only create/view their own tickets - once
 * created, resolution is admin-only (see the admin SupportTicketResource).
 */
class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'Support Ticket';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('partner_id', Auth::guard('partner')->id());
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->required()->rows(5),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Tiket')
                    ->schema([
                        Infolists\Components\TextEntry::make('subject'),
                        Infolists\Components\TextEntry::make('status')->badge(),
                        Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')->dateTime(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Penyelesaian')
                    ->visible(fn (SupportTicket $record) => filled($record->resolution_note))
                    ->schema([
                        Infolists\Components\TextEntry::make('resolution_note')->label('Catatan Penyelesaian')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'gray',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
        ];
    }
}
