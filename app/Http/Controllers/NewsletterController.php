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

        $source = $request->input('source', 'coming-soon');

        NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            ['source' => $source]
        );

        $message = $source === 'blog'
            ? 'Terima kasih! Anda telah berlangganan newsletter kami.'
            : 'Terima kasih! Kami akan menghubungi Anda segera setelah website ini rilis.';

        return back()->with('status', $message);
    }
}
