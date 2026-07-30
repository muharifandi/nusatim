<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Final Review (2026-07-30): RBAC replaces the old free-text
     * "Workflow Approval" placeholder. Data seed lives in a migration
     * (not a separate seeder) so it runs automatically under
     * RefreshDatabase in tests too, and so existing staff `users` rows
     * don't get locked out of the admin panel the moment permission
     * checks start being enforced across resources.
     *
     * Modules scoped to the Portal Partner/Sales Partner Management
     * feature only - the 12 pre-existing site-profile CMS resources
     * (Client/Faq/Menu/Page/Post/PricingPlan/Project/Promotion/Service/
     * TeamMember/Testimonial/ContactMessage) are out of scope.
     */
    public const MODULES = [
        'partner',
        'lead',
        'partner_project',
        'commission_scheme',
        'commission',
        'withdrawal',
        'marketing_material',
        'partner_sales_target',
        'report',
        'partner_setting',
        'support_ticket',
        'role',
        'user',
        'workflow_assignment',
        'audit_log',
    ];

    public const VERBS = ['view', 'create', 'update', 'delete', 'approve', 'reject', 'assign', 'export'];

    public function up(): void
    {
        $permissionNames = [];

        foreach (self::MODULES as $module) {
            foreach (self::VERBS as $verb) {
                $permissionNames[] = "{$module}.{$verb}";
            }
        }

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permissionNames);

        // Every staff user that already exists keeps full access once
        // gating goes live - safe for both real deployments and tests
        // (RefreshDatabase runs this migration before any User factory
        // rows exist, so this loop is a no-op there; UserFactory handles
        // newly-created test users separately).
        User::all()->each(fn (User $user) => $user->assignRole($superAdmin));
    }

    public function down(): void
    {
        Role::where('name', 'Super Admin')->delete();

        Permission::where(function ($query) {
            foreach (self::MODULES as $module) {
                $query->orWhere('name', 'like', "{$module}.%");
            }
        })->delete();
    }
};
