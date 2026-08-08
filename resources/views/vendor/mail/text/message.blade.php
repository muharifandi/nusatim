@php($siteSettings = \App\Models\SiteSetting::current())
<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            {{ $siteSettings->company_name ?: config('app.name') }}
        </x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        <x-slot:subcopy>
            <x-mail::subcopy>
                {{ $subcopy }}
            </x-mail::subcopy>
        </x-slot:subcopy>
    @endisset

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            © {{ date('Y') }} {{ $siteSettings->legal_name ?: $siteSettings->company_name ?: config('app.name') }}. @lang('All rights reserved.')
            Email ini dikirim otomatis, mohon tidak membalas langsung ke alamat ini.
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
