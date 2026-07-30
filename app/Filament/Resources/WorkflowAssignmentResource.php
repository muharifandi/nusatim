<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\WorkflowAssignmentResource\Pages;
use App\Models\WorkflowAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class WorkflowAssignmentResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'workflow_assignment';

    protected static ?string $model = WorkflowAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Workflow Assignment';

    protected static ?string $navigationGroup = 'RBAC & Sistem';

    public static function canCreate(): bool
    {
        // The 6 rows are fixed and seeded once (seed_workflow_assignments) -
        // admin only ever edits which Role approves each one.
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('role_id')
                    ->label('Role Approver')
                    ->options(fn () => Role::pluck('name', 'id'))
                    ->placeholder('Siapa saja dengan permission approve di modul ini')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workflow')
                    ->label('Workflow')
                    ->formatStateUsing(fn (string $state) => WorkflowAssignment::WORKFLOWS[$state] ?? $state),
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Role Approver')
                    ->placeholder('Siapa saja (tidak dibatasi)'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWorkflowAssignments::route('/'),
        ];
    }
}
