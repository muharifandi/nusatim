<x-filament-panels::page>
    <x-filament::section>
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[200px]">
                <label class="fi-fo-field-wrp-label text-sm font-medium">Produk</label>
                <select wire:model.live="serviceFilter" class="fi-input mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">Semua Produk</option>
                    @foreach($this->services() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
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

    <div
        x-data
        x-init="window.pipelineInitBoard($el, $wire)"
        class="pipeline-board mt-6 flex gap-4 overflow-x-auto pb-4"
    >
        @foreach(\App\Filament\Partner\Pages\Pipeline::STATUSES as $status => $label)
            @php($columnLeads = $this->getLeadsByStatus()[$status])
            <div
                data-status="{{ $status }}"
                class="pipeline-column flex w-72 shrink-0 flex-col rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2 dark:border-gray-700">
                    <span class="text-sm font-semibold">{{ $label }}</span>
                    <x-filament::badge color="gray">{{ $columnLeads->count() }}</x-filament::badge>
                </div>
                <div class="pipeline-column-body flex min-h-[120px] flex-col gap-2 p-2">
                    @foreach($columnLeads as $lead)
                        <div
                            draggable="true"
                            data-lead-id="{{ $lead->id }}"
                            class="pipeline-card cursor-move rounded-lg border border-gray-200 bg-white p-3 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-900"
                        >
                            <div class="font-medium">{{ $lead->name }}</div>
                            <div class="text-xs text-gray-500">{{ $lead->phone }}</div>
                            @if($lead->estimated_value)
                                <div class="text-xs text-gray-500">Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
