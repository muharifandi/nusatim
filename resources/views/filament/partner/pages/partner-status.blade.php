<x-filament-panels::page>
    @php($partner = $this->partner())

    @if($partner->status === 'rejected')
        <x-filament::section>
            <x-slot name="heading">Pendaftaran Belum Bisa Disetujui</x-slot>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Mohon maaf, pendaftaran Anda sebagai partner belum bisa kami setujui saat ini.
            </p>

            @if($partner->rejection_reason)
                <div class="mt-4 rounded-lg border p-4 text-sm">
                    {{ $partner->rejection_reason }}
                </div>
            @endif
        </x-filament::section>
    @elseif($partner->status === 'suspended')
        <x-filament::section>
            <x-slot name="heading">Akun Disuspend</x-slot>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Akun partner Anda untuk sementara disuspend oleh admin dan tidak bisa mengakses portal. Hubungi tim kami kalau ini di luar dugaan Anda.
            </p>

            @if($partner->rejection_reason)
                <div class="mt-4 rounded-lg border p-4 text-sm">
                    {{ $partner->rejection_reason }}
                </div>
            @endif
        </x-filament::section>
    @else
        <x-filament::section>
            <x-slot name="heading">Menunggu Persetujuan Admin</x-slot>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Terima kasih sudah mendaftar. Pendaftaran Anda sedang ditinjau oleh tim kami. Kami akan mengirimkan email begitu status pendaftaran berubah.
            </p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
