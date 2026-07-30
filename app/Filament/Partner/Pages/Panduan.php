<?php

namespace App\Filament\Partner\Pages;

use Filament\Pages\Page;

class Panduan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Panduan';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.partner.pages.panduan';

    public function getTitle(): string
    {
        return 'Panduan Penggunaan Partner';
    }
}
