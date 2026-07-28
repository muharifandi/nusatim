<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateTeamMember extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = TeamMemberResource::class;
}
