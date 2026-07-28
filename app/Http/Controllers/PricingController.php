<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\PricingPlan;

class PricingController extends Controller
{
    public function index()
    {
        $page = Page::bySlug('pricing');

        return view('pricing.index', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'plans' => PricingPlan::active()->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
