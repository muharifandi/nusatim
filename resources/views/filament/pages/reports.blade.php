<x-filament-panels::page>
    <x-filament::section>
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="fi-fo-field-wrp-label text-sm font-medium">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom" class="fi-input mt-1 block rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
            </div>
            <div>
                <label class="fi-fo-field-wrp-label text-sm font-medium">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo" class="fi-input mt-1 block rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
            </div>
        </div>
    </x-filament::section>

    @php($partner = $this->partnerReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Partner</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('partner') }}">Export CSV</a>
        </x-slot>

        <div class="mb-4 flex flex-wrap gap-4">
            @foreach($partner['by_status'] as $status => $count)
                <x-filament::badge>{{ $status }}: {{ $count }}</x-filament::badge>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-1 pr-4">Nama</th>
                        <th class="py-1 pr-4">Status</th>
                        <th class="py-1 pr-4">Lead</th>
                        <th class="py-1 pr-4">Customer</th>
                        <th class="py-1 pr-4">Komisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partner['rows'] as $row)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="py-1 pr-4">{{ $row['name'] }}</td>
                            <td class="py-1 pr-4">{{ $row['status'] }}</td>
                            <td class="py-1 pr-4">{{ $row['leads'] }}</td>
                            <td class="py-1 pr-4">{{ $row['customers'] }}</td>
                            <td class="py-1 pr-4">Rp{{ number_format($row['commission'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    @php($leads = $this->leadReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Lead</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('lead') }}">Export CSV</a>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-1 pr-4">Partner</th>
                        <th class="py-1 pr-4">Per Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $partnerName => $statuses)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="py-1 pr-4">{{ $partnerName }}</td>
                            <td class="py-1 pr-4">
                                @foreach($statuses as $status => $count)
                                    <span class="mr-2">{{ $status }}: {{ $count }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    @php($projects = $this->projectReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Project</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('project') }}">Export CSV</a>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-1 pr-4">Status</th>
                        <th class="py-1 pr-4">Jumlah</th>
                        <th class="py-1 pr-4">Total Budget</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $row)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="py-1 pr-4">{{ $row['status'] }}</td>
                            <td class="py-1 pr-4">{{ $row['count'] }}</td>
                            <td class="py-1 pr-4">Rp{{ number_format($row['total_budget'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    @php($closing = $this->closingReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Closing (Tren per Bulan)</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('closing') }}">Export CSV</a>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-1 pr-4">Bulan</th>
                        <th class="py-1 pr-4">Jumlah Closing</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($closing as $row)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="py-1 pr-4">{{ $row['month'] }}</td>
                            <td class="py-1 pr-4">{{ $row['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    @php($commission = $this->commissionReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Komisi</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('commission') }}">Export CSV</a>
        </x-slot>

        <p class="mb-2 text-sm font-semibold">Total: Rp{{ number_format($commission['total'], 0, ',', '.') }}</p>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500">Per Status</p>
                @foreach($commission['by_status'] as $status => $amount)
                    <div class="text-sm">{{ $status }}: Rp{{ number_format($amount, 0, ',', '.') }}</div>
                @endforeach
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500">Per Partner</p>
                @foreach($commission['by_partner'] as $name => $amount)
                    <div class="text-sm">{{ $name }}: Rp{{ number_format($amount, 0, ',', '.') }}</div>
                @endforeach
            </div>
        </div>
    </x-filament::section>

    @php($withdrawal = $this->withdrawalReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Withdrawal</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('withdrawal') }}">Export CSV</a>
        </x-slot>

        <p class="mb-2 text-sm font-semibold">Total: Rp{{ number_format($withdrawal['total'], 0, ',', '.') }}</p>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500">Per Status</p>
                @foreach($withdrawal['by_status'] as $status => $amount)
                    <div class="text-sm">{{ $status }}: Rp{{ number_format($amount, 0, ',', '.') }}</div>
                @endforeach
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-500">Per Partner</p>
                @foreach($withdrawal['by_partner'] as $name => $amount)
                    <div class="text-sm">{{ $name }}: Rp{{ number_format($amount, 0, ',', '.') }}</div>
                @endforeach
            </div>
        </div>
    </x-filament::section>

    @php($performance = $this->partnerPerformanceReport())
    <x-filament::section>
        <x-slot name="heading">Laporan Performa Partner (Ranking)</x-slot>
        <x-slot name="headerEnd">
            <a class="text-sm underline" target="_blank" href="{{ $this->exportUrl('performance') }}">Export CSV</a>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-1 pr-4">#</th>
                        <th class="py-1 pr-4">Partner</th>
                        <th class="py-1 pr-4">Customer</th>
                        <th class="py-1 pr-4">Komisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($performance as $i => $row)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="py-1 pr-4">{{ $i + 1 }}</td>
                            <td class="py-1 pr-4">{{ $row['name'] }}</td>
                            <td class="py-1 pr-4">{{ $row['customers'] }}</td>
                            <td class="py-1 pr-4">Rp{{ number_format($row['commission'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">Laporan Nilai Penjualan</x-slot>
        <p class="text-2xl font-semibold">Rp{{ number_format($this->totalSalesReport(), 0, ',', '.') }}</p>
        <p class="text-sm text-gray-500">Total omzet dari seluruh partner{{ ($this->dateFrom || $this->dateTo) ? ' pada periode yang dipilih' : '' }}.</p>
    </x-filament::section>
</x-filament-panels::page>
