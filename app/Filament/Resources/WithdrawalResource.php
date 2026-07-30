<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use App\Models\WorkflowAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class WithdrawalResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'withdrawal';

    protected static ?string $model = Withdrawal::class;

    protected static ?string $modelLabel = 'Withdrawal';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Withdrawal';

    protected static ?string $navigationGroup = 'Commission & Withdrawal';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')->label('Nominal')->disabled(),
                Forms\Components\TextInput::make('bank_name')->disabled(),
                Forms\Components\TextInput::make('bank_account_number')->disabled(),
                Forms\Components\TextInput::make('bank_account_holder')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                Forms\Components\Textarea::make('note')->disabled(),
                Forms\Components\Placeholder::make('documents')
                    ->label('Dokumen')
                    ->content(function (?Withdrawal $record) {
                        if (! $record) {
                            return '-';
                        }

                        $links = ['ktp' => 'Foto KTP'];

                        if ($record->proof_of_transfer_path) {
                            $links['proof'] = 'Bukti Transfer';
                        }

                        return new HtmlString(
                            collect($links)
                                ->map(fn ($label, $type) => '<a class="underline" target="_blank" href="'.route('withdrawal.documents.show', [$record, $type]).'">'.$label.'</a>')
                                ->implode(' &middot; ')
                        );
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->searchable(),
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
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Withdrawal $record) => $record->status === 'pending'
                        && auth()->user()?->can('withdrawal.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::WITHDRAWAL_APPROVAL, auth()->user()))
                    ->action(fn (Withdrawal $record) => $record->approve()),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Withdrawal $record) => in_array($record->status, ['pending', 'approved'])
                        && auth()->user()?->can('withdrawal.reject')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::WITHDRAWAL_APPROVAL, auth()->user()))
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Alasan Reject')->required(),
                    ])
                    ->action(fn (Withdrawal $record, array $data) => $record->reject($data['reason'])),
                Tables\Actions\Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Withdrawal $record) => $record->status === 'approved')
                    ->form([
                        Forms\Components\FileUpload::make('proof_of_transfer_path')
                            ->label('Bukti Transfer')
                            ->disk('partner_documents')
                            ->directory('withdrawals')
                            ->required(),
                    ])
                    ->action(function (Withdrawal $record, array $data) {
                        $record->markPaid($data['proof_of_transfer_path']);

                        Notification::make()
                            ->title('Withdrawal ditandai selesai dibayar')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWithdrawals::route('/'),
        ];
    }
}
