<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\PartnerProjectResource\Pages;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\Service;
use App\Models\WorkflowAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerProjectResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'partner_project';

    protected static ?string $model = PartnerProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Project Board';

    protected static ?string $navigationGroup = 'Partner Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description'),
                Forms\Components\Select::make('service_id')
                    ->label('Produk')
                    ->options(fn () => Service::active()->pluck('title', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('budget')->numeric()->prefix('Rp'),
                Forms\Components\TextInput::make('location'),
                Forms\Components\DatePicker::make('deadline'),
                Forms\Components\Select::make('difficulty')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']),
                Forms\Components\TextInput::make('commission_value')
                    ->label('Estimasi Nilai Komisi')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Angka referensi untuk partner, bukan hasil hitungan skema komisi (belum ada).'),
                Forms\Components\TextInput::make('progress')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->helperText('Default sementara: bisa diupdate admin di sini, atau oleh partner lewat action "Update Progress" di Customer - keputusan resmi siapa yang berwenang belum ditetapkan.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('service.title')->label('Produk')->placeholder('-'),
                Tables\Columns\TextColumn::make('partner.name')->label('Partner')->placeholder('-'),
                Tables\Columns\TextColumn::make('budget')->money('IDR')->placeholder('-'),
                Tables\Columns\TextColumn::make('progress')->suffix('%')->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'available' => 'info',
                        'pending_approval' => 'warning',
                        'assigned', 'in_progress' => 'primary',
                        'closed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->visible(fn (PartnerProject $record) => $record->status === 'draft'
                        && auth()->user()?->can('partner_project.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::PROJECT_APPROVAL, auth()->user()))
                    ->action(fn (PartnerProject $record) => $record->publish()),
                Tables\Actions\Action::make('assignPartner')
                    ->label('Assign Partner')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (PartnerProject $record) => in_array($record->status, ['available', 'draft']))
                    ->form([
                        Forms\Components\Select::make('partner_id')
                            ->label('Partner')
                            ->options(fn () => Partner::where('status', 'approved')->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn (PartnerProject $record, array $data) => $record->assignDirectly(Partner::findOrFail($data['partner_id']))),
                Tables\Actions\Action::make('approveClaim')
                    ->label('Approve Claim')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PartnerProject $record) => $record->status === 'pending_approval'
                        && auth()->user()?->can('partner_project.approve')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::PROJECT_CLAIM, auth()->user()))
                    ->action(fn (PartnerProject $record) => $record->approveClaim()),
                Tables\Actions\Action::make('rejectClaim')
                    ->label('Reject Claim')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (PartnerProject $record) => $record->status === 'pending_approval'
                        && auth()->user()?->can('partner_project.reject')
                        && WorkflowAssignment::userIsAuthorizedFor(WorkflowAssignment::PROJECT_CLAIM, auth()->user()))
                    ->action(fn (PartnerProject $record) => $record->rejectClaim()),
                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-lock-closed')
                    ->visible(fn (PartnerProject $record) => in_array($record->status, ['assigned', 'in_progress']))
                    ->requiresConfirmation()
                    ->action(fn (PartnerProject $record) => $record->close()),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePartnerProjects::route('/'),
        ];
    }
}
