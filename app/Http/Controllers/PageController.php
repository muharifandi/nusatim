<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\Service;
use App\Models\TeamMember;

class PageController extends Controller
{
    public function about()
    {
        $page = Page::bySlug('about');

        return view('pages.about', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'services' => Service::active()->limit(3)->get(),
            'teamMembers' => TeamMember::active()->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
