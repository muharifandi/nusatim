@if($siteSettings->google_analytics_id ?? null)
	<!-- Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteSettings->google_analytics_id }}"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', '{{ $siteSettings->google_analytics_id }}');
	</script>
@endif
