<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta Data -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $metaTitle ?? 'Coming Soon | '.($siteSettings->company_name ?? 'Nusatim') }}</title>

	<!-- SEO Meta Tags -->
	<meta name="description" content="{{ $metaDescription ?? ($siteSettings->company_name ?? 'Nusatim').' is working on something new. Stay tuned for updates.' }}">
	<meta name="robots" content="{{ $robots ?? 'noindex, follow' }}">

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
	<!-- Coming Soon responsive fix (keeps the countdown a single row on mobile) -->
	<link rel="stylesheet" href="{{ asset('assets/css/coming-soon-responsive.css') }}" type="text/css">
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

			<section class="coming-soon-wrap has-animation">
				<ul class="shape-holder has-animation">
					@for ($i = 0; $i < 7; $i++)
						<li>
							<svg width="945px" height="945px">
								<path opacity="0.1" fill="rgb(47, 30, 134)"
									d="M472.500,0.000 C733.454,0.000 945.000,211.546 945.000,472.500 C945.000,733.454 733.454,945.000 472.500,945.000 C211.545,945.000 -0.000,733.454 -0.000,472.500 C-0.000,211.546 211.545,0.000 472.500,0.000 Z" />
							</svg>
						</li>
					@endfor
				</ul>
				<div class="container">
					<div class="coming-soon-box">
						<div class="coming-soon-logo translate-bottom-75 opacity-animation transition-150 transition-delay-100">
							<img src="{{ $siteSettings->logo_light ? asset($siteSettings->logo_light) : asset('media/logo-light.png') }}" alt="{{ $siteSettings->company_name }}">
						</div>
						<div class="translate-zoomout-50 opacity-animation transition-150 transition-delay-100">
							<div id="countdown" class="countdown" data-target="{{ $page?->field('countdown_target') }}"></div>
						</div>
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
							<div class="sub-title">{{ $page?->field('sub_title', "We're Coming Soon..") }}</div>
						</div>
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
							<h1 class="main-title">{{ $page?->field('main_title', "We're working on our new website, stay tuned!") }}</h1>
						</div>
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
							<button type="button" class="item-btn btn-ghost btn-light" data-toggle="modal" data-target="#notifyModal">{{ $page?->field('button_text', 'Notify Us') }}</button>
						</div>
					</div>
				</div>
			</section>

		</div>
	</div>

	<!-- Notify Us Modal -->
	<div class="modal fade" id="notifyModal" tabindex="-1" role="dialog" aria-labelledby="notifyModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="notifyModalLabel">Notify Us</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					@if(session('status'))
						<div class="alert alert-success">{{ session('status') }}</div>
					@endif
					<p>Masukkan email Anda, kami akan memberi tahu segera setelah website ini rilis.</p>
					<form method="POST" action="{{ route('newsletter.subscribe') }}">
						@csrf
						<input type="hidden" name="source" value="coming-soon">
						<div class="form-group">
							<label for="notifyEmail">Email</label>
							<input
								type="email"
								name="email"
								id="notifyEmail"
								class="form-control @error('email') is-invalid @enderror"
								placeholder="nama@email.com"
								value="{{ old('email') }}"
								required
							>
							@error('email')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<button type="submit" class="btn-fill btn-gradient w-100">Kirim</button>
					</form>
				</div>
			</div>
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

	@if(session('status') || $errors->any())
		<script>
			$(function () { $('#notifyModal').modal('show'); });
		</script>
	@endif

</body>

</html>
