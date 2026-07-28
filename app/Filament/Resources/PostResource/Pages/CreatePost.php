<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Concerns\RedirectsToResourceIndex;

class CreatePost extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PostResource::class;
}
