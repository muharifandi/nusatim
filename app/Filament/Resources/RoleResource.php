<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesModule;
use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use AuthorizesModule;

    protected static string $permissionModule = 'role';

    protected static ?string $model = Role::class;

    protected static ?string $modelLabel = 'Role';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Role & Permission';

    protected static ?string $navigationGroup = 'RBAC & Sistem';

    protected static ?int $navigationSort = 1;

    /**
     * Matches the MODULES list in seed_rbac_modules_and_super_admin_role -
     * human labels here, not the raw slugs, since these render directly as
     * section headings in the permission matrix below.
     */
    public const MODULE_LABELS = [
        'partner' => 'Partner',
        'lead' => 'Lead',
        'partner_project' => 'Project Board',
        'commission_scheme' => 'Commission Scheme',
        'commission' => 'Commission',
        'withdrawal' => 'Withdrawal',
        'marketing_material' => 'Marketing Material',
        'partner_sales_target' => 'Sales Target',
        'report' => 'Reports',
        'partner_setting' => 'Partner Settings',
        'support_ticket' => 'Support Ticket',
        'role' => 'Role & Permission',
        'user' => 'Staff User',
        'workflow_assignment' => 'Workflow Assignment',
        'audit_log' => 'Audit Log',
    ];

    /** Matches the VERBS list in the same seed migration. */
    public const VERB_LABELS = [
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'assign' => 'Assign',
        'export' => 'Export',
    ];

    /**
     * A flat "module.verb" CheckboxList of all ~120 permissions read as a
     * wall of technical strings wrapping across 4 cramped columns - hard to
     * scan and easy to misclick. Rendering one compact CheckboxList per
     * module (short View/Create/Update/... labels, no module prefix
     * repeated 8 times) is what an actual permission matrix looks like
     * elsewhere (e.g. Nova, Spatie's own filament-shield). Bypasses
     * ->relationship() entirely - state lives at permission_verbs.$module
     * as an array of verbs, translated to/from real permission names in
     * the Create/Edit table actions (see RoleResource\Pages\ManageRoles
     * and the editAction() override below).
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\Grid::make(['default' => 1, 'md' => 2, 'xl' => 3])
                    ->schema(
                        collect(self::MODULE_LABELS)
                            ->map(fn (string $label, string $module) => Forms\Components\Section::make($label)
                                ->compact()
                                ->schema([
                                    Forms\Components\CheckboxList::make("permission_verbs.{$module}")
                                        ->hiddenLabel()
                                        ->options(self::VERB_LABELS)
                                        ->columns(2)
                                        ->bulkToggleable(),
                                ]))
                            ->values()
                            ->all()
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('permissions_count')->counts('permissions')->label('Jumlah Permission'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Role $record): array {
                        $data['permission_verbs'] = [];

                        foreach ($record->permissions as $permission) {
                            [$module, $verb] = explode('.', $permission->name, 2);
                            $data['permission_verbs'][$module][] = $verb;
                        }

                        return $data;
                    })
                    ->using(function (Role $record, array $data): Role {
                        $record->update(['name' => $data['name']]);
                        $record->syncPermissions(self::permissionNamesFromVerbs($data['permission_verbs'] ?? []));

                        return $record;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Role $record) => $record->name !== 'Super Admin'),
            ]);
    }

    /**
     * @param  array<string, array<int, string>>  $verbsByModule
     * @return array<int, string>
     */
    public static function permissionNamesFromVerbs(array $verbsByModule): array
    {
        $names = [];

        foreach ($verbsByModule as $module => $verbs) {
            foreach ($verbs as $verb) {
                $names[] = "{$module}.{$verb}";
            }
        }

        return $names;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
