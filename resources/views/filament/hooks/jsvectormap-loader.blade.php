{{--
    Loaded via a panel render hook (not the widget view itself) so this is
    part of the initial hard page load. Filament dashboard widgets lazy-load
    their real markup through a follow-up Livewire request, and browsers
    never execute <script> tags that arrive via that kind of DOM patch - so
    a script embedded directly in the widget view is silently dead code.
    Defining window.cmwInitMap here, where it's guaranteed to actually run,
    lets the widget's x-init just call the already-loaded function.
--}}
<link rel="stylesheet" href="{{ asset('vendor/jsvectormap/jsvectormap.min.css') }}">
<script src="{{ asset('vendor/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('vendor/jsvectormap/world.js') }}"></script>
<script>
	window.cmwInitMap = function (el, values, colors, attempt) {
		attempt = attempt || 0;
		if (! el || el.dataset.initialized === '1') {
			return;
		}
		if (typeof jsVectorMap === 'undefined') {
			if (attempt < 50) {
				setTimeout(function () { window.cmwInitMap(el, values, colors, attempt + 1); }, 100);
			}
			return;
		}
		el.dataset.initialized = '1';

		// Identity map (code -> code) so OrdinalScale.getValue(code) looks
		// the region's own code up in `colors` (code -> hex) and returns a
		// distinct color per country, instead of a single-hue intensity
		// gradient. Countries with no data are left out of both maps
		// entirely, so they just keep regionStyle.initial's grey fill.
		var identity = {};
		Object.keys(colors).forEach(function (code) { identity[code] = code; });

		new jsVectorMap({
			selector: '#' + el.id,
			map: 'world',
			zoomButtons: true,
			zoomOnScroll: true,
			zoomMax: 8,
			selectedRegions: [],
			regionStyle: {
				initial: { fill: '#dfe1e6', stroke: '#ffffff', strokeWidth: 0.5 },
				hover: { fill: '#9ca3af', cursor: 'pointer' },
			},
			series: {
				regions: [{
					attribute: 'fill',
					scale: colors,
					values: identity,
				}],
			},
			onRegionTooltipShow: function (event, tooltip, code) {
				var count = values[code] || 0;
				tooltip.text(tooltip.text() + ': ' + count + ' kunjungan', true);
			},
		});
	};
</script>
