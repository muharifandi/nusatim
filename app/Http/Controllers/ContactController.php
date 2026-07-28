<?php

namespace App\Http\Controllers;

use App\Mail\ContactAutoReply;
use App\Models\Client;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        $page = Page::bySlug('contact');

        return view('contact.show', [
            'page' => $page,
            'metaTitle' => $page?->meta_title,
            'metaDescription' => $page?->meta_description,
            'metaKeywords' => $page?->meta_keywords,
            'ogImage' => $page?->og_image,
            'clients' => Client::active()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create($validated);

        // The submitter's inbox is unaffected if this fails (wrong/typo'd
        // email, mail server hiccup, etc.) - the message itself is already
        // saved above, so we only log the failure rather than blocking the
        // visitor's form submission on it.
        try {
            Mail::to($contactMessage->email)->send(
                new ContactAutoReply($contactMessage, SiteSetting::current())
            );
        } catch (\Throwable $e) {
            Log::warning('Contact auto-reply email failed to send.', [
                'contact_message_id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('status', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}
