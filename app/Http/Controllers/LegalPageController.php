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
        return view('legal.index', [
            'metaTitle' => 'Dokumen Legal',
            'legalPages' => LegalPage::active()->get(),
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
            'metaDescription' => $legalPage->meta_description,
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
