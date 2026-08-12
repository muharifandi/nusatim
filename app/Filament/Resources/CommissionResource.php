<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\CommissionResource\Pages;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\WorkflowAssignment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommissionResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'commission';

    protected static ?string $model = Commission::class;

    protected static ?string $modelLabel = 'Commission';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Commission';

    protected static ?string $navigationGroup = 'Commission & Withdrawal';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        // Dibuat lewat action "Generate Komisi" / "Bonus Komisi" di header,
        // bukan form create biasa - keduanya butuh logic khusus.
        return false;
    }

    /**
     * `->visible()` alone only hides the button in the UI - Filament's
     * mountTableAction()/callMountedTableAction() never re-checks it (or
     * ->authorize()) before running the action closure, so a Livewire
     * request crafted directly against this table (available to anyone who
     * can reach the page, i.e. anyone with commission.view) can still
     * invoke the action. Every closure that changes money-affecting state
     * must assert this itself.
     */
    private static function assertCanApproveOrPay(): void
    {
        abort_unless(
            (bool) auth()->user()?->can('commission.approve')
                && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::COMMISSION_APPROVAL, auth()->user()),
            403
        );
    }

    private static function assertCanReject(): void
    {
        abort_unless(
            (bool) auth()->user()?->can('commission.reject')
                && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::COMMISSION_APPROVAL, auth()->user()),
            403
        );
    }

    private static function assertCanCreate(): void
    {
        abort_unless((bool) auth()->user()?->can('commission.create'), 403);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer')->placeholder('-')->searchable(),
                Tables\Columns\TextColumn::make('customer.service.title')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('project_value')->label('Nilai Project')->money('IDR')->placeholder('-'),
                Tables\Columns\TextColumn::make('percentage')->label('Persentase')->suffix('%')->placeholder('-'),
                Tables\Columns\TextColumn::make('amount')->label('Nominal')->money('IDR'),
                Tables\Columns\IconColumn::make('is_bonus')->label('Bonus')->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        'waiting_client_payment' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate Komisi')
                    ->icon('heroicon-o-calculator')
                    ->visible(fn () => auth()->user()?->can('commission.create'))
                    ->form([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->options(fn () => Customer::whereDoesntHave('commission')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        static::assertCanCreate();

                        return Commission::generateForCustomer(Customer::findOrFail($data['customer_id']));
                    }),
                Tables\Actions\Action::make('addBonus')
                    ->label('Bonus Komisi')
                    ->icon('heroicon-o-gift')
                    ->visible(fn () => auth()->user()?->can('commission.create'))
                    ->form([
                        Forms\Components\Select::make('partner_id')
                            ->label('Partner')
                            ->options(fn () => Partner::where('status', 'approved')->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer (opsional)')
                            ->options(fn () => Customer::pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Bonus')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\Textarea::make('note')->label('Catatan'),
                    ])
                    ->action(function (array $data) {
                        static::assertCanCreate();

                        return Commission::create([
                            'partner_id' => $data['partner_id'],
                            'customer_id' => $data['customer_id'] ?? null,
                            'amount' => $data['amount'],
                            'type' => 'bonus',
                            'status' => 'pending',
                            'is_bonus' => true,
                            'note' => $data['note'] ?? null,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('waitingClientPayment')
                    ->label('Waiting Client Payment')
                    ->icon('heroicon-o-clock')
                    ->visible(fn (Commission $record) => $record->status === 'pending')
                    ->action(fn (Commission $record) => $record->markWaitingClientPayment()),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Commission $record) => in_array($record->status, ['pending', 'waiting_client_payment'])
                        && auth()->user()?->can('commission.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::COMMISSION_APPROVAL, auth()->user()))
                    ->action(function (Commission $record) {
                        static::assertCanApproveOrPay();
                        $record->approve();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Commission $record) => in_array($record->status, ['pending', 'waiting_client_payment'])
                        && auth()->user()?->can('commission.reject')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::COMMISSION_APPROVAL, auth()->user()))
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Alasan Reject')->required(),
                    ])
                    ->action(function (Commission $record, array $data) {
                        static::assertCanReject();
                        $record->reject($data['reason']);
                    }),
                Tables\Actions\Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Commission $record) => $record->status === 'approved'
                        && auth()->user()?->can('commission.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::COMMISSION_APPROVAL, auth()->user()))
                    ->action(function (Commission $record) {
                        static::assertCanApproveOrPay();
                        $record->markPaid();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommissions::route('/'),
        ];
    }
}
