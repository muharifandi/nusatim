<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Panduan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Panduan';

    protected static ?string $navigationGroup = 'Bantuan';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.panduan';

    public function getTitle(): string
    {
        return 'Panduan Penggunaan Admin';
    }
}
