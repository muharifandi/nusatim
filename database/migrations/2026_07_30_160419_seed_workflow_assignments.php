<?php

use App\Models\WorkflowAssignment;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Seeds the fixed 6-row list of workflows, all starting unassigned
     * (role_id null = anyone with the module's approve permission may
     * act). Lives in a migration (not a seeder) so it runs automatically
     * under RefreshDatabase in tests too.
     */
    public function up(): void
    {
        foreach (array_keys(WorkflowAssignment::WORKFLOWS) as $workflow) {
            WorkflowAssignment::firstOrCreate(['workflow' => $workflow]);
        }
    }

    public function down(): void
    {
        WorkflowAssignment::whereIn('workflow', array_keys(WorkflowAssignment::WORKFLOWS))->delete();
    }
};
