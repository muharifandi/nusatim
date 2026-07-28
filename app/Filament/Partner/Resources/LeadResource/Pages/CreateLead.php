<?php

namespace App\Filament\Partner\Resources\LeadResource\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Partner\Resources\LeadResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLead extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partner_id'] = Auth::guard('partner')->id();

        return $data;
    }
}
