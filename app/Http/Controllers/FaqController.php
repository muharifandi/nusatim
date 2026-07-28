<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;

class FaqController extends Controller
{
    public function index()
    {
        $page = Page::bySlug('faq');

        return view('faq.index', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'faqs' => Faq::active()->get(),
            'recentPosts' => Post::published()->limit(5)->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
