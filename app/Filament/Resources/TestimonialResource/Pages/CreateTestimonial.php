<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Resources\TestimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreateTestimonial extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = TestimonialResource::class;
}
