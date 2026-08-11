<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta Data -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Resource hints - fire the font DNS/TLS handshake before the render-blocking
	     stylesheets below get parsed, instead of only starting it once the browser
	     reaches the font <link> further down. -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<title>{{ $metaTitle ?? $siteSettings->default_meta_title ?? $siteSettings->company_name }}</title>

	<!-- SEO Meta Tags -->
	<meta name="description" content="{{ $metaDescription ?? $siteSettings->default_meta_description }}">
	<meta name="keywords" content="{{ $metaKeywords ?? $siteSettings->default_meta_keywords }}">
	<meta name="robots" content="{{ $robots ?? 'index, follow' }}">
	<link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">

	<!-- Open Graph / Facebook, WhatsApp, LinkedIn -->
	<meta property="og:type" content="{{ $ogType ?? 'website' }}">
	<meta property="og:site_name" content="{{ $siteSettings->company_name }}">
	<meta property="og:title" content="{{ $metaTitle ?? $siteSettings->default_meta_title ?? $siteSettings->company_name }}">
	<meta property="og:description" content="{{ $metaDescription ?? $siteSettings->default_meta_description }}">
	<meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
	<meta property="og:image" content="{{ $ogImage ?? $siteSettings->default_og_image }}">
	<meta property="og:locale" content="en_US">

	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="{{ $metaTitle ?? $siteSettings->default_meta_title ?? $siteSettings->company_name }}">
	<meta name="twitter:description" content="{{ $metaDescription ?? $siteSettings->default_meta_description }}">
	<meta name="twitter:image" content="{{ $ogImage ?? $siteSettings->default_og_image }}">

	@stack('structured-data')

	@include('partials.google-analytics')

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ $siteSettings->favicon ? asset($siteSettings->favicon) : asset('media/favicon.png') }}">

	<!-- Dependency Styles - only the plugins every page actually uses
	     (bootstrap grid/components, icon fonts, mobile menu). Anything
	     page-specific (owl.carousel, magnific-popup, counterup, parallaxie,
	     validator) is pushed from the individual view/partial that needs
	     it via @push('styles'); several other bundled plugins (select2,
	     wow.js, page-piling, nivo-slider, slick, isotope, knob, tilt,
	     theia-sticky-sidebar, gmap3, countdown, jquery.parallax-scroll)
	     were confirmed unused anywhere in resources/views and dropped
	     entirely rather than gated - see app.js for the corresponding
	     init calls, all guarded so removing the plugin scripts is safe. -->
	<link rel="stylesheet" href="{{ asset('dependencies/bootstrap/css/bootstrap.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/fontawesome/css/all.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/flaticon/flaticon.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/meanmenu/css/meanmenu.min.css') }}" type="text/css">

	<!-- Site Stylesheet -->
	<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" type="text/css">
	<!-- Animation Stylesheet -->
	<link rel="stylesheet" href="{{ asset('dependencies/animation-css/animation-css.css') }}" type="text/css">
	@if($siteSettings->enable_image_skeleton ?? true)
		<!-- Image Skeleton Loading -->
		<link rel="stylesheet" href="{{ asset('assets/css/img-skeleton.css') }}" type="text/css">
	@endif

	<!-- Google Web Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:300,400,500,600,700,800&display=swap" rel="stylesheet">

	@stack('styles')
</head>

<body class="{{ $bodyClass ?? '' }}"@if(isset($bodyId)) id="{{ $bodyId }}"@endif>

	<div id="preloader" class="tlp-preloader">
		<div class="animation-preloader">
			<div class="tlp-spinner"></div>
			<img src="{{ $siteSettings->preloader_logo ? asset($siteSettings->preloader_logo) : asset('media/preloader.png') }}" alt="Preloader" class="skel-off">
		</div>
	</div>

	<div id="wrapper" class="wrapper">
		<a href="#main_content" data-type="section-switch" class="return-to-top">
			<i class="fas fa-angle-double-up"></i>
		</a>

		<div id="main_content">

			@include('partials.nav')

			{{ $slot ?? '' }}
			@yield('content')

			@include('partials.footer')

		</div>
	</div>

	<!-- Template Search -->
	<div id="template-search" class="template-search">
		<button type="button" class="close">&times;</button>
		<form class="search-form">
			<input type="search" value="" placeholder="Type here........" />
			<button type="submit" class="search-btn"><i class="flaticon-search"></i></button>
		</form>
	</div>

	@include('partials.offcanvas')

	@if(request()->routeIs('home'))
		@include('partials.promo-popup')
	@endif
	@include('partials.cookie-consent')

	<!-- Dependency Scripts - core only (jQuery/bootstrap/meanmenu are used
	     site-wide). Page-specific plugins (owl.carousel, magnific-popup,
	     counterup+waypoints, parallaxie, validator) are pushed here via
	     @push('scripts') by the view/partial that needs them - BEFORE
	     assets/js/app.js below, since app.js runs immediately on load
	     (not deferred to document-ready) and feature-detects each plugin
	     with an `if ($.fn.pluginName)`-style guard at that exact point. -->
	<script src="{{ asset('dependencies/popper.js/popper.min.js') }}"></script>
	<script src="{{ asset('dependencies/jquery/jquery.min.js') }}"></script>
	<script src="{{ asset('dependencies/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('dependencies/meanmenu/js/jquery.meanmenu.min.js') }}"></script>

	@stack('scripts')

	<!-- Site Scripts -->
	<script>
		window.__siteConfig = {
			mobileLogo: "{{ $siteSettings->logo_mobile ? asset($siteSettings->logo_mobile) : asset('media/logo-mobile.png') }}",
			homeUrl: "{{ route('home') }}"
		};
	</script>
	<script src="{{ asset('assets/js/app.js') }}"></script>
	@if($siteSettings->enable_image_skeleton ?? true)
		<script src="{{ asset('assets/js/img-skeleton.js') }}"></script>
	@endif
</body>

</html>
