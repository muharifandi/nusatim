<?php

namespace App\Filament\Partner\Resources\CustomerResource\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Partner\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = CustomerResource::class;
}
