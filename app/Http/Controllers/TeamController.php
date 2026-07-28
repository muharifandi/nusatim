<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\TeamMember;

class TeamController extends Controller
{
    public function index()
    {
        $page = Page::bySlug('team');

        return view('team.index', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'members' => TeamMember::active()->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
