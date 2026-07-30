<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\RoleResource\Pages\ManageRoles::class)
            ->callAction('create', data: [
                'name' => 'Staff Lead Viewer',
                'permissions' => [
                    Permission::where('name', 'lead.view')->value('id'),
                ],
            ]);

        $role = Role::where('name', 'Staff Lead Viewer')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('lead.view'));
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
