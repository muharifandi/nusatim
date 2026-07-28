<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;

class ComingSoonController extends Controller
{
    public function index(): View
    {
        $page = Page::bySlug('coming-soon');

        return view('coming-soon', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'robots' => 'noindex, follow',
        ]);
    }
}
