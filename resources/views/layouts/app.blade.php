<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta Data -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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

	<!-- Dependency Styles -->
	<link rel="stylesheet" href="{{ asset('dependencies/bootstrap/css/bootstrap.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/fontawesome/css/all.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/select2/css/select2.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/flaticon/flaticon.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/wow/css/animate.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/page-piling/css/jquery.pagepiling.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/nivo-slider/css/nivo-slider.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/meanmenu/css/meanmenu.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/magnific-popup/css/magnific-popup.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/owl.carousel/css/owl.carousel.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/owl.carousel/css/owl.theme.default.min.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/slick/css/slick.css') }}" type="text/css">
	<link rel="stylesheet" href="{{ asset('dependencies/slick/css/slick-theme.css') }}" type="text/css">

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

	<!-- Dependency Scripts -->
	<script src="{{ asset('dependencies/popper.js/popper.min.js') }}"></script>
	<script src="{{ asset('dependencies/jquery/jquery.min.js') }}"></script>
	<script src="{{ asset('dependencies/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('dependencies/jquery.appear/jquery.appear.js') }}"></script>
	<script src="{{ asset('dependencies/jquery.parallax-scroll/jquery.parallax-scroll.js') }}"></script>
	<script src="{{ asset('dependencies/gmap3/js/gmap3.min.js') }}"></script>
	<script src="{{ asset('dependencies/owl.carousel/js/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('dependencies/slick/js/slick.min.js') }}"></script>
	<script src="{{ asset('dependencies/counter-up/jquery.counterup.min.js') }}"></script>
	<script src="{{ asset('dependencies/waypoints/jquery.waypoints.min.js') }}"></script>
	<script src="{{ asset('dependencies/select2/js/select2.min.js') }}"></script>
	<script src="{{ asset('dependencies/isotope-layout/isotope.pkgd.min.js') }}"></script>
	<script src="{{ asset('dependencies/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
	<script src="{{ asset('dependencies/meanmenu/js/jquery.meanmenu.min.js') }}"></script>
	<script src="{{ asset('dependencies/Parallaxie-master/parallaxie.js') }}"></script>
	<script src="{{ asset('dependencies/nivo-slider/js/jquery.nivo.slider.js') }}"></script>
	@stack('nivo-slider-init')
	<script src="{{ asset('dependencies/wow/js/wow.min.js') }}"></script>
	<script src="{{ asset('dependencies/knob/jquery.knob.js') }}"></script>
	<script src="{{ asset('dependencies/countdown/jquery.countdown.min.js') }}"></script>
	<script src="{{ asset('dependencies/page-piling/js/jquery.pagepiling.min.js') }}"></script>
	<script src="{{ asset('dependencies/tilt/tilt.jquery.min.js') }}"></script>
	<script src="{{ asset('dependencies/theia-sticky-sidebar/theia-sticky-sidebar.min.js') }}"></script>
	<script src="{{ asset('dependencies/theia-sticky-sidebar/resize-sensor.min.js') }}"></script>
	<script src="{{ asset('dependencies/magnific-popup/js/jquery.magnific-popup.min.js') }}"></script>
	<script src="{{ asset('dependencies/validator/validator.min.js') }}"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}"></script>

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

	@stack('scripts')
</body>

</html>
