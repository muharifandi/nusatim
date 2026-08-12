<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Partner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_migration_creates_all_15_modules_with_8_permissions_and_a_super_admin_role(): void
    {
        $this->assertSame(15 * 8, Permission::count());
        $this->assertTrue(Permission::where('name', 'lead.view')->exists());
        $this->assertTrue(Permission::where('name', 'support_ticket.assign')->exists());

        $superAdmin = Role::where('name', 'Super Admin')->firstOrFail();
        $this->assertSame(Permission::count(), $superAdmin->permissions()->count());
    }

    public function test_factory_created_user_defaults_to_super_admin_and_can_access_every_gated_resource(): void
    {
        $admin = User::factory()->create();

        $this->assertTrue($admin->hasRole('Super Admin'));

        $this->actingAs($admin)->get(route('filament.admin.resources.partners.index'))->assertOk();
        $this->actingAs($admin)->get(route('filament.admin.resources.leads.index'))->assertOk();
        $this->actingAs($admin)->get(route('filament.admin.resources.roles.index'))->assertOk();
    }

    public function test_admin_can_create_a_role_with_permissions(): void
    {
        $admin = User::factory()->create();

        // Permission matrix form: one CheckboxList per module
        // (permission_verbs.$module), not a flat list of permission IDs -
        // grouping by module with short View/Create/... labels is what
        // replaced the original single 120-item wall of "module.verb"
        // strings that was hard to scan.
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->callAction('create', data: [
                'name' => 'Staff Lead Viewer',
                'permission_verbs' => [
                    'lead' => ['view'],
                ],
            ]);

        $role = Role::where('name', 'Staff Lead Viewer')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('lead.view'));
        $this->assertFalse($role->hasPermissionTo('lead.delete'));
    }

    public function test_admin_can_edit_a_roles_permissions_via_the_grouped_matrix(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Editable Role', 'guard_name' => 'web']);
        $role->syncPermissions(['lead.view']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->mountTableAction('edit', $role)
            ->assertTableActionDataSet(['permission_verbs' => ['lead' => ['view']]])
            ->setTableActionData([
                'name' => 'Editable Role',
                'permission_verbs' => [
                    'lead' => ['view', 'update'],
                    'commission' => ['approve'],
                ],
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('lead.view'));
        $this->assertTrue($role->hasPermissionTo('lead.update'));
        $this->assertTrue($role->hasPermissionTo('commission.approve'));
        $this->assertFalse($role->hasPermissionTo('lead.delete'));
    }

    public function test_admin_can_assign_a_user_to_a_role(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->create();
        $role = Role::create(['name' => 'Staff Lead Viewer', 'guard_name' => 'web']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\UserResource\Pages\ManageUsers::class)
            ->mountTableAction('edit', $staff)
            ->setTableActionData(['name' => $staff->name, 'email' => $staff->email, 'roles' => [$role->id]])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertTrue($staff->fresh()->hasRole('Staff Lead Viewer'));
    }

    public function test_a_staff_user_cannot_grant_a_role_permissions_they_do_not_have_themselves(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions(['role.view', 'role.create', 'role.update', 'lead.view']);

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->callAction('create', data: [
                'name' => 'Escalated Role',
                'permission_verbs' => [
                    'lead' => ['view'],
                    'withdrawal' => ['approve'],
                ],
            ])
            ->assertHasActionErrors();

        $this->assertFalse(Role::where('name', 'Escalated Role')->exists());
    }

    public function test_a_staff_user_can_create_a_role_with_only_permissions_they_already_have(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions(['role.view', 'role.create', 'role.update', 'lead.view']);

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->callAction('create', data: [
                'name' => 'Lead Viewer Only',
                'permission_verbs' => [
                    'lead' => ['view'],
                ],
            ])
            ->assertHasNoActionErrors();

        $role = Role::where('name', 'Lead Viewer Only')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('lead.view'));
    }

    public function test_the_super_admin_role_cannot_be_renamed_or_stripped_of_permissions(): void
    {
        $admin = User::factory()->create();
        $superAdmin = Role::where('name', 'Super Admin')->firstOrFail();
        $totalPermissions = Permission::count();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->mountTableAction('edit', $superAdmin)
            ->setTableActionData([
                'name' => 'Renamed Admin',
                'permission_verbs' => ['lead' => ['view']],
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $superAdmin->refresh();
        $this->assertSame('Super Admin', $superAdmin->name);
        $this->assertSame($totalPermissions, $superAdmin->permissions()->count());
    }

    public function test_the_super_admin_role_cannot_be_deleted_even_via_a_direct_action_call(): void
    {
        $admin = User::factory()->create();
        $superAdmin = Role::where('name', 'Super Admin')->firstOrFail();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->call('mountTableAction', 'delete', $superAdmin->getKey())
            ->call('callMountedTableAction');

        $this->assertTrue(Role::whereKey($superAdmin->id)->exists());
    }

    public function test_a_staff_user_cannot_change_their_own_roles(): void
    {
        $viewerRole = Role::create(['name' => 'Lead Viewer', 'guard_name' => 'web']);
        $viewerRole->syncPermissions(['lead.view', 'user.view', 'user.update']);

        $staff = User::factory()->create();
        $staff->syncRoles([$viewerRole]);

        $superAdmin = Role::where('name', 'Super Admin')->firstOrFail();

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\UserResource\Pages\ManageUsers::class)
            ->mountTableAction('edit', $staff)
            ->setTableActionData(['name' => $staff->name, 'email' => $staff->email, 'roles' => [$superAdmin->id]])
            ->callMountedTableAction();

        $this->assertFalse($staff->fresh()->hasRole('Super Admin'));
        $this->assertTrue($staff->fresh()->hasRole('Lead Viewer'));
    }

    public function test_user_without_the_relevant_permission_cannot_view_a_gated_resource(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions([]);

        $this->actingAs($staff)
            ->get(route('filament.admin.resources.partners.index'))
            ->assertForbidden();
    }

    public function test_user_with_only_lead_view_permission_can_view_leads_but_not_partners(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions(['lead.view']);

        $this->actingAs($staff)
            ->get(route('filament.admin.resources.leads.index'))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('filament.admin.resources.partners.index'))
            ->assertForbidden();
    }
}
