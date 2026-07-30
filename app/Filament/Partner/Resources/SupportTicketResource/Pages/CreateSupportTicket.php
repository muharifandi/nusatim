<?php

namespace App\Filament\Partner\Resources\SupportTicketResource\Pages;

use App\Filament\Partner\Resources\SupportTicketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partner_id'] = Auth::guard('partner')->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
