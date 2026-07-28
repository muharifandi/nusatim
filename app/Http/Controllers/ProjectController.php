<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Page;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $page = Page::bySlug('portfolio');

        return view('portfolio.index', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'projects' => Project::active()->get(),
            'clients' => Client::active()->get(),
        ]);
    }

    public function show(Project $project)
    {
        return view('portfolio.show', [
            'project' => $project,
            'metaTitle' => $project->meta_title ?? $project->title,
            'metaDescription' => $project->meta_description ?? $project->description,
            'ogImage' => $project->og_image ?? $project->image,
            'otherProjects' => Project::active()->where('id', '!=', $project->id)->limit(6)->get(),
        ]);
    }
}
