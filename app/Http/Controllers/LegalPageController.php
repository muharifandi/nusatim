<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LegalPageController extends Controller
{
    public function index()
    {
        $legalPages = LegalPage::active()->get();

        return view('legal.index', [
            'metaTitle' => 'Dokumen Legal',
            // Every other page type passes a real metaDescription; this one
            // was silently falling through to the sitewide default (which
            // has nothing to do with legal documents) - list what's
            // actually on the page instead.
            'metaDescription' => $legalPages->isNotEmpty()
                ? 'Kebijakan dan dokumen resmi: '.$legalPages->pluck('title')->join(', ').'.'
                : 'Kebijakan dan dokumen resmi perusahaan.',
            'legalPages' => $legalPages,
        ]);
    }

    public function show(LegalPage $legalPage)
    {
        if (! $legalPage->is_active) {
            throw new NotFoundHttpException();
        }

        return view('legal.show', [
            'legalPage' => $legalPage,
            'metaTitle' => $legalPage->meta_title ?? $legalPage->title,
            // meta_description is admin-editable and often left blank for
            // these (policy/document pages get filled in less often than
            // blog posts) - fall back to a plain-text excerpt of the actual
            // content instead of silently losing the search snippet to the
            // generic sitewide default.
            'metaDescription' => $legalPage->meta_description
                ?: Str::limit(trim(strip_tags((string) $legalPage->content)), 160),
            'metaKeywords' => $legalPage->meta_keywords,
            'otherLegalPages' => LegalPage::active()->where('id', '!=', $legalPage->id)->get(),
        ]);
    }

    public function pdf(LegalPage $legalPage)
    {
        if (! $legalPage->is_active) {
            throw new NotFoundHttpException();
        }

        $pdf = Pdf::loadView('legal.pdf', [
            'legalPage' => $legalPage,
            'siteSettings' => SiteSetting::current(),
            'logoDataUri' => $this->logoDataUri(),
        ]);

        return $pdf->download(Str::slug($legalPage->title).'.pdf');
    }

    /**
     * dompdf needs a locally-readable image (data URI is the most reliable
     * across environments - no dependency on dompdf's remote-fetch config or
     * the 'media' disk's public URL being reachable from the server itself).
     */
    private function logoDataUri(): ?string
    {
        $siteSettings = SiteSetting::current();
        $path = $siteSettings->logo_dark ?: $siteSettings->logo_light;

        if (! $path || ! Storage::disk('media')->exists($path)) {
            return null;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'png';

        return "data:image/{$extension};base64,".base64_encode(Storage::disk('media')->get($path));
    }
}
