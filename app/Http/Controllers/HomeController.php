<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $page = Page::bySlug('home');

        return view('home', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'services' => Service::active()->limit(6)->get(),
            'posts' => Post::published()->limit(3)->get(),
            'projects' => Project::active()->limit(3)->get(),
            'testimonials' => Testimonial::active()->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
