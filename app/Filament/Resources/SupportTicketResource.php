<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WorkflowAssignment;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'support_ticket';

    protected static ?string $model = SupportTicket::class;

    protected static ?string $modelLabel = 'Support Ticket';

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'Support Ticket';

    protected static ?string $navigationGroup = 'Marketing & Support';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        // Tiket selalu dibuat partner sendiri lewat /partner/support-tickets.
        return false;
    }

    public static function canEdit($record): bool
    {
        // Tidak ada form edit mentah - status/assignment/resolution cuma
        // lewat action (assign/resolve/close) di bawah.
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Tiket')
                    ->schema([
                        Infolists\Components\TextEntry::make('partner.name')->label('Partner'),
                        Infolists\Components\TextEntry::make('subject'),
                        Infolists\Components\TextEntry::make('status')->badge(),
                        Infolists\Components\TextEntry::make('assignee.name')->label('Ditugaskan Ke')->placeholder('-'),
                        Infolists\Components\TextEntry::make('description')->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->searchable(),
                Tables\Columns\TextColumn::make('subject')->searchable(),
                Tables\Columns\TextColumn::make('assignee.name')->label('Ditugaskan Ke')->placeholder('-'),
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
                Tables\Actions\Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (SupportTicket $record) => in_array($record->status, ['open', 'in_progress'])
                        && auth()->user()?->can('support_ticket.assign')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::SUPPORT_TICKET, auth()->user()))
                    ->form([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Staff')
                            ->options(fn () => User::pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn (SupportTicket $record, array $data) => $record->assign(User::findOrFail($data['assigned_to']))),
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (SupportTicket $record) => ! in_array($record->status, ['resolved', 'closed'])
                        && auth()->user()?->can('support_ticket.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::SUPPORT_TICKET, auth()->user()))
                    ->form([
                        Forms\Components\Textarea::make('resolution_note')
                            ->label('Catatan Penyelesaian')
                            ->required(),
                    ])
                    ->action(fn (SupportTicket $record, array $data) => $record->resolve($data['resolution_note'])),
                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-lock-closed')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (SupportTicket $record) => $record->status !== 'closed'
                        && auth()->user()?->can('support_ticket.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::SUPPORT_TICKET, auth()->user()))
                    ->action(fn (SupportTicket $record) => $record->close()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSupportTickets::route('/'),
        ];
    }
}
