<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            ['source' => $request->input('source', 'coming-soon')]
        );

        return back()->with('status', 'Terima kasih! Kami akan menghubungi Anda segera setelah website ini rilis.');
    }
}
