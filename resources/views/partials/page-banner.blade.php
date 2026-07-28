@php
	$breadcrumbParent = $breadcrumbParent ?? 'Pages';
@endphp
<section id="page-banner" class="page-banner bg-gradient-layout6 has-animation">
	<ul class="shape-holder">
		<li>
			<div class="translate-top-50 opacity-animation transition-200 transition-delay-10">
				<svg width="827px" height="827px">
					<defs>
						<linearGradient id="shape1" x1="0%" x2="50%" y1="86.603%" y2="0%">
							<stop offset="0%" stop-color="rgb(82,63,255)" stop-opacity="0.6" />
							<stop offset="100%" stop-color="rgb(117,14,213)" stop-opacity="0.6" />
						</linearGradient>
					</defs>
					<path fill="url(#shape1)" d="M413.500,0.000 C641.870,0.000 827.000,185.130 827.000,413.500 C827.000,641.870 641.870,827.000 413.500,827.000 C185.130,827.000 -0.000,641.870 -0.000,413.500 C-0.000,185.130 185.130,0.000 413.500,0.000 Z"/>
				</svg>
			</div>
		</li>
		<li>
			<div class="translate-top-50 opacity-animation transition-100 transition-delay-100">
				<svg width="615px" height="615px">
					<defs>
						<linearGradient id="shape2" x1="0%" x2="50%" y1="86.603%" y2="0%">
							<stop offset="0%" stop-color="rgb(82,63,255)" stop-opacity="0.6" />
							<stop offset="100%" stop-color="rgb(109,26,223)" stop-opacity="0.6" />
						</linearGradient>
					</defs>
					<path fill="url(#shape2)" d="M307.500,0.000 C477.327,0.000 615.000,137.673 615.000,307.500 C615.000,477.327 477.327,615.000 307.500,615.000 C137.672,615.000 -0.000,477.327 -0.000,307.500 C-0.000,137.673 137.672,0.000 307.500,0.000 Z"/>
				</svg>
			</div>
		</li>
		<li>
			<div class="translate-top-50 opacity-animation transition-50 transition-delay-200">
				<svg width="441px" height="441px">
					<defs>
						<linearGradient id="shape3" x1="0%" x2="50%" y1="86.603%" y2="0%">
							<stop offset="13%" stop-color="rgb(82,63,255)" stop-opacity="0.4" />
							<stop offset="100%" stop-color="rgb(130,33,221)" stop-opacity="0.4" />
						</linearGradient>
					</defs>
					<path fill="url(#shape3)" d="M220.500,0.000 C342.279,0.000 441.000,98.721 441.000,220.500 C441.000,342.279 342.279,441.000 220.500,441.000 C98.721,441.000 -0.000,342.279 -0.000,220.500 C-0.000,98.721 98.721,0.000 220.500,0.000 Z"/>
				</svg>
			</div>
		</li>
	</ul>
	<div class="container">
		<div class="breadcrumbs-area">
			<h1>{{ $title }}</h1>
			<ul>
				<li><a href="{{ route('home') }}">Home</a></li>
				<li>{{ $breadcrumbParent }}</li>
			</ul>
		</div>
	</div>
</section>
