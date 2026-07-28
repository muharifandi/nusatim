<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use App\Filament\Resources\PromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreatePromotion extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PromotionResource::class;
}
