<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateFaq extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = FaqResource::class;
}
