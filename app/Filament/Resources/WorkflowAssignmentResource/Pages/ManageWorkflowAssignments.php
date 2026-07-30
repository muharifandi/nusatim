<?php

namespace App\Filament\Resources\WorkflowAssignmentResource\Pages;

use App\Filament\Resources\WorkflowAssignmentResource;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkflowAssignments extends ManageRecords
{
    protected static string $resource = WorkflowAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
