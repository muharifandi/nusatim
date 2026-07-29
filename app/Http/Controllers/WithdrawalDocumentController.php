<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a withdrawal's KTP photo or admin-uploaded proof of transfer from
 * the private `partner_documents` disk. A partner may only view documents
 * on their own withdrawals; any authenticated admin (web guard) may view
 * any withdrawal's documents.
 */
class WithdrawalDocumentController extends Controller
{
    protected const FIELDS = [
        'ktp' => 'ktp_path',
        'proof' => 'proof_of_transfer_path',
    ];

    public function show(Request $request, Withdrawal $withdrawal, string $type): StreamedResponse
    {
        if (! array_key_exists($type, self::FIELDS)) {
            abort(404);
        }

        if (Auth::guard('partner')->check() && Auth::guard('partner')->id() !== $withdrawal->partner_id) {
            throw new AuthorizationException;
        }

        $path = $withdrawal->{self::FIELDS[$type]};

        if (! $path || ! Storage::disk('partner_documents')->exists($path)) {
            abort(404);
        }

        return Storage::disk('partner_documents')->response($path);
    }
}
