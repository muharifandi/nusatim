<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateContactMessage extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ContactMessageResource::class;
}
