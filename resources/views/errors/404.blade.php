<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta Data -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Page Not Found | {{ $siteSettings->company_name ?? 'Nusatim' }}</title>

	<!-- SEO Meta Tags -->
	<meta name="description" content="The page you are looking for could not be found. Return to the homepage to explore our technology and digital solutions.">
	<meta name="robots" content="noindex, follow">

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
</head>

<body>

	<div id="preloader" class="tlp-preloader">
		<div class="animation-preloader">
			<div class="tlp-spinner"></div>
			<img src="{{ $siteSettings->preloader_logo ? asset($siteSettings->preloader_logo) : asset('media/preloader.png') }}" alt="Preloader" class="skel-off">
		</div>
	</div>

	<div id="wrapper" class="wrapper">
		<div id="main_content">

			<section class="error-page-wrap has-animation">
				<div class="container">
					<div class="error-page">
						<div class="item-figure">
							<div class="translate-zoomout-50 opacity-animation transition-200 transition-delay-100">
								<img src="{{ asset('media/illustration/404.png') }}" alt="404">
							</div>
						</div>
						<div class="item-content">
							<div class="translate-bottom-75 opacity-animation transition-200 transition-delay-100">
								<h2 class="item-title">Page Not Found</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-200 transition-delay-300">
								<p>We're sorry, the page you have looked for does not exist in our database! Maybe go to our home page or try to use a search?</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-200 transition-delay-400">
								<a href="{{ route('home') }}" class="btn-fill btn-gradient">Go Back To Home</a>
							</div>
						</div>
					</div>
				</div>
			</section>

		</div>
	</div>

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
	<script src="{{ asset('dependencies/wow/js/wow.min.js') }}"></script>
	<script src="{{ asset('dependencies/knob/jquery.knob.js') }}"></script>
	<script src="{{ asset('dependencies/countdown/jquery.countdown.min.js') }}"></script>
	<script src="{{ asset('dependencies/page-piling/js/jquery.pagepiling.min.js') }}"></script>
	<script src="{{ asset('dependencies/tilt/tilt.jquery.min.js') }}"></script>
	<script src="{{ asset('dependencies/theia-sticky-sidebar/theia-sticky-sidebar.min.js') }}"></script>
	<script src="{{ asset('dependencies/theia-sticky-sidebar/resize-sensor.min.js') }}"></script>
	<script src="{{ asset('dependencies/magnific-popup/js/jquery.magnific-popup.min.js') }}"></script>
	<script src="{{ asset('dependencies/validator/validator.min.js') }}"></script>

	<!-- Site Scripts -->
	<script src="{{ asset('assets/js/app.js') }}"></script>
	@if($siteSettings->enable_image_skeleton ?? true)
		<script src="{{ asset('assets/js/img-skeleton.js') }}"></script>
	@endif

</body>

</html>
