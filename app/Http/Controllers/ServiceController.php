<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $page = Page::bySlug('services');

        return view('services.index', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'services' => Service::active()->get(),
            'clients' => Client::active()->get(),
        ]);
    }

    public function show(Service $service)
    {
        return view('services.show', [
            'service' => $service,
            'metaTitle' => $service->meta_title ?? $service->title,
            'metaDescription' => $service->meta_description ?? $service->short_description,
            'ogImage' => $service->og_image ?? $service->image,
            'otherServices' => Service::active()->where('id', '!=', $service->id)->limit(5)->get(),
            'clients' => Client::active()->get(),
        ]);
    }
}
