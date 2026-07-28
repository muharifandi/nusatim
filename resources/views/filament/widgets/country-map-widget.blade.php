<x-filament-widgets::widget>
	<x-filament::section>
		<x-slot name="heading">Peta Pengunjung Berdasarkan Negara</x-slot>
		<x-slot name="description">{{ $totalVisits }} kunjungan dari {{ $totalCountries }} negara terdeteksi</x-slot>

		<div class="cmw-wrap">
			<div class="cmw-map-col">
				<div
					id="cmw-world-map"
					wire:ignore
					x-data="{}"
					x-init="window.cmwInitMap($el, @js($mapValues), @js($countryColors))"
				></div>
			</div>
			<div class="cmw-list-col">
				@forelse($topCountries as $row)
					@php
						$pct = $totalVisits > 0 ? round(($row->total / $totalVisits) * 100) : 0;
						$color = $countryColors[$row->country_code] ?? '#5a49f8';
					@endphp
					<div class="cmw-row">
						<span class="cmw-flag">{{ \App\Filament\Widgets\CountryMapWidget::flagEmoji($row->country_code) }}</span>
						<span class="cmw-name">{{ $row->country_name }}</span>
						<span class="cmw-count">{{ $row->total }}</span>
						<div class="cmw-bar-track">
							<div class="cmw-bar-fill" style="width: {{ $pct }}%; background: {{ $color }};"></div>
						</div>
					</div>
				@empty
					<p class="cmw-empty">Belum ada data negara pengunjung. Data akan muncul otomatis setelah beberapa saat.</p>
				@endforelse
			</div>
		</div>
	</x-filament::section>

	<style>
		.cmw-wrap {
			display: flex;
			flex-direction: column;
			gap: 1.25rem;
		}
		.cmw-map-col {
			width: 100%;
		}
		#cmw-world-map {
			height: 440px;
			width: 100%;
		}
		.cmw-list-col {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
			gap: 0.5rem 1.5rem;
		}
		.cmw-row {
			display: grid;
			grid-template-columns: 22px 1fr auto;
			align-items: center;
			gap: 0.5rem;
			margin-bottom: 0.65rem;
		}
		.cmw-flag { font-size: 16px; line-height: 1; }
		.cmw-name { font-size: 0.8125rem; color: rgb(107 114 128); }
		.cmw-count { font-size: 0.8125rem; font-weight: 600; color: rgb(55 65 81); }
		.cmw-bar-track {
			grid-column: 1 / -1;
			height: 5px;
			border-radius: 999px;
			background: rgba(90, 73, 248, 0.1);
			overflow: hidden;
			margin-top: 2px;
		}
		.cmw-bar-fill {
			height: 100%;
			border-radius: 999px;
			background: #5a49f8;
		}
		.cmw-empty { font-size: 0.8125rem; color: rgb(107 114 128); }
		.jvm-tooltip { background-color: #14142b !important; }

		:root[data-theme="dark"] .cmw-name { color: rgb(156 163 175); }
		:root[data-theme="dark"] .cmw-count { color: rgb(229 231 235); }
	</style>
</x-filament-widgets::widget>
