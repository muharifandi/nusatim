@php
    // Both checks matter: the session flag alone proves nothing (a partner
    // could set it on their own session), it only means something combined
    // with a genuinely still-authenticated 'web' guard session - which a
    // partner has no way to fake.
    $isImpersonating = session()->has('impersonating_admin_id')
        && \Illuminate\Support\Facades\Auth::guard('web')->check();
@endphp

@if($isImpersonating)
    <div style="background:#78350f;color:#fef3c7;padding:10px 20px;font-size:0.85rem;display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
        <span>🔓 Anda login sebagai <strong>{{ \Illuminate\Support\Facades\Auth::guard('partner')->user()?->name }}</strong> (mode impersonasi oleh admin).</span>
        <a href="{{ route('partner.stop-impersonating') }}" style="text-decoration:underline;font-weight:600;color:#fef3c7;">Kembali ke Admin</a>
    </div>
@endif
