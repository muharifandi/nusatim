@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Tentang Kami')])

	{{-- ===== Service preview ===== --}}
	<section class="service-wrap-layout11 section-padding-md bg-color-light">
		<div class="container">
			<div class="row has-animation">
				@foreach($services as $service)
					<div class="col-lg-4 col-12">
						<div class="translate-bottom-100 opacity-animation transition-100 transition-delay-{{ 100 + $loop->index * 50 }}">
							<div class="service-box-layout9">
								<div class="item-icon icon-bg-california">
									<div class="icon-color">
										<i class="{{ $service->icon ?: 'flaticon-marketing' }}"></i>
									</div>
								</div>
								<h3 class="item-title"><a href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a></h3>
								<p>{{ $service->short_description }}</p>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	{{-- ===== About ===== --}}
	<section class="section-padding-bottom-md about-wrap-layout10 has-animation">
		<div class="container">
			<div class="row d-flex align-items-center">
				<div class="col-lg-6 col-12">
					<div class="about-box-layout10">
						<div class="content-holder">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<h2 class="item-title">{{ $page?->field('title', 'Tentang ' . $siteSettings->company_name) }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<p>{{ $page?->field('text') }}</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<a href="{{ route('contact') }}" class="btn-fill btn-gradient">{{ $page?->field('read_more_text', 'Hubungi Kami') }}</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-12">
					<div class="about-box-layout10">
						<div class="figure-holder">
							<div class="translate-right-75 opacity-animation transition-200 transition-delay-400">
								<img src="{{ $page?->field('about_image') ? asset($page->field('about_image')) : asset('media/about/about5.png') }}" alt="About">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ===== Feature ===== --}}
	<section class="feature-wrap-layout9 section-padding-md-equal">
		<div class="container">
			<div class="section-heading heading-dark heading-layout4">
				<div class="heading-sub-title">{{ $page?->field('feature_subtitle', 'Tentang Perusahaan Kami') }}</div>
				<h2 class="heading-main-title">{{ $page?->field('feature_title') }}</h2>
			</div>
			<div class="row align-items-center">
				<div class="col-lg-6 col-12 has-animation">
					<div class="translate-left-75 opacity-animation transition-200 transition-delay-10">
						<div class="feature-box-layout9">
							<div class="item-figure">
								<img src="{{ $page?->field('feature_image') ? asset($page->field('feature_image')) : asset('media/feature/feature10.png') }}" alt="Feature">
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-12 has-animation">
					<div class="translate-bottom-75 opacity-animation transition-200 transition-delay-10">
						<div class="feature-box-layout9">
							<div class="item-content">
								<h2 class="item-title">{{ $page?->field('feature_heading') }}</h2>
								<p>{{ $page?->field('feature_text') }}</p>
								<ul class="list-item">
									@foreach([1, 2, 3] as $i)
										@if($page?->field("feature_item_{$i}_title"))
											<li class="icon-bg-{{ ['dodger-blue-2', 'radical-red', 'west-side'][$i - 1] }}">
												<div class="item-icon">
													<i class="{{ $page?->field("feature_item_{$i}_icon", 'flaticon-innovation') }}"></i>
												</div>
												<div class="inner-item-content">
													<h3 class="inner-title">{{ $page?->field("feature_item_{$i}_title") }}</h3>
													<p>{{ $page?->field("feature_item_{$i}_text") }}</p>
												</div>
											</li>
										@endif
									@endforeach
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ===== History ===== --}}
	@if($page?->field('history_1_year'))
	<section class="history-wrap-layout1 has-animation">
		<div class="container">
			<div class="section-heading heading-dark heading-layout4">
				<div class="heading-sub-title">{{ $page?->field('history_subtitle', 'Perjalanan Kami') }}</div>
				<h2 class="heading-main-title">{{ $page?->field('history_title') }}</h2>
			</div>
			<ul class="history-inner-layout1">
				@for($i = 1; $i <= 4; $i++)
					@if($page?->field("history_{$i}_year"))
						<li class="history-box-layout1">
							<div class="item-year">{{ $page->field("history_{$i}_year") }}</div>
							<div class="item-content">
								<h3>{{ $page->field("history_{$i}_title") }}</h3>
								<p>{{ $page->field("history_{$i}_text") }}</p>
							</div>
						</li>
					@endif
				@endfor
			</ul>
		</div>
	</section>
	@endif

	{{-- ===== Experience / video ===== --}}
	<section class="about-wrap-layout8">
		<ul class="animated-buble has-animation">
			<li>
				<div class="translate-right-100 opacity-animation transition-200 transition-delay-100">
					<svg width="616px" height="616px">
						<defs>
							<linearGradient id="animated-buble1" x1="0%" x2="50%" y1="86.603%" y2="0%">
								<stop offset="0%" stop-color="rgb(90,73,248)" stop-opacity="1" />
								<stop offset="100%" stop-color="rgb(123,100,242)" stop-opacity="1" />
							</linearGradient>
						</defs>
						<path fill="url(#animated-buble1)" opacity="0.4" d="M308.000,-0.000 C478.104,-0.000 616.000,137.896 616.000,308.000 C616.000,478.103 478.104,616.000 308.000,616.000 C137.896,616.000 -0.000,478.103 -0.000,308.000 C-0.000,137.896 137.896,-0.000 308.000,-0.000 Z"/>
					</svg>
				</div>
			</li>
			<li>
				<div class="translate-right-100 opacity-animation transition-200 transition-delay-100">
					<svg width="489px" height="489px">
						<defs>
							<linearGradient id="animated-buble2" x1="0%" x2="50%" y1="86.603%" y2="0%">
								<stop offset="0%" stop-color="rgb(90,73,248)" stop-opacity="1" />
								<stop offset="100%" stop-color="rgb(123,100,242)" stop-opacity="1" />
							</linearGradient>
						</defs>
						<path fill="url(#animated-buble2)" opacity="0.4" d="M244.500,-0.000 C379.534,-0.000 489.000,109.466 489.000,244.500 C489.000,379.533 379.534,489.000 244.500,489.000 C109.466,489.000 -0.000,379.533 -0.000,244.500 C-0.000,109.466 109.466,-0.000 244.500,-0.000 Z"/>
					</svg>
				</div>
			</li>
			<li>
				<div class="translate-right-100 opacity-animation transition-200 transition-delay-200">
					<svg width="411px" height="411px">
						<defs>
							<linearGradient id="animated-buble3" x1="0%" x2="50%" y1="86.603%" y2="0%">
								<stop offset="0%" stop-color="rgb(90,73,248)" stop-opacity="1" />
								<stop offset="100%" stop-color="rgb(123,100,242)" stop-opacity="1" />
							</linearGradient>
						</defs>
						<path fill="url(#animated-buble3)" opacity="0.4" d="M205.500,-0.000 C318.994,-0.000 411.000,92.005 411.000,205.500 C411.000,318.994 318.994,411.000 205.500,411.000 C92.005,411.000 -0.000,318.994 -0.000,205.500 C-0.000,92.005 92.005,-0.000 205.500,-0.000 Z"/>
					</svg>
				</div>
			</li>
		</ul>
		<div class="container-fluid">
			<div class="row d-flex align-items-center">
				<div class="col-xl-6 col-12 pl-0 pr-0">
					<div class="about-box-layout8">
						<div class="figure-holder">
							<img src="{{ $page?->field('video_image') ? asset($page->field('video_image')) : asset('media/about/about-back.jpg') }}" alt="About">
							@if($page?->field('video_url'))
								<a class="play-btn popup-youtube" href="{{ $page->field('video_url') }}">
									<div class="item-icon"><i class="fas fa-play"></i></div>
								</a>
							@endif
						</div>
					</div>
				</div>
				<div class="col-xl-6 col-12 compress-right-side">
					<div class="about-box-layout8">
						<div class="content-holder has-animation">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<h2 class="item-title">{{ $page?->field('experience_title') }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<p>{{ $page?->field('experience_text') }}</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
								<ul class="list-item">
									@foreach(explode('|', (string) $page?->field('experience_items', '')) as $item)
										@if(trim($item) !== '')
											<li>{{ trim($item) }}</li>
										@endif
									@endforeach
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ===== Team ===== --}}
	@if($teamMembers->isNotEmpty())
	<section id="team-wrap-layout1" class="section-padding-md">
		<div class="container">
			<div class="section-heading heading-dark heading-layout4">
				<div class="heading-sub-title">{{ $page?->field('team_eyebrow', 'Tim Kami') }}</div>
				<h2 class="heading-main-title">{{ $page?->field('team_heading', 'Kami Selalu Mencari Talenta Software Terbaik') }}</h2>
			</div>
			<div class="row has-animation rc-carousel nav-control-layout4 col-full-width" data-options='{"trigger_start": 991, "trigger_end": 0}' data-loop="true" data-items="30" data-margin="10" data-autoplay="false" data-autoplay-timeout="5000" data-smart-speed="700"
			 data-dots="false" data-nav="true" data-nav-speed="false" data-r-x-small="1" data-r-x-small-nav="true" data-r-x-small-dots="false" data-r-x-medium="1" data-r-x-medium-nav="true" data-r-x-medium-dots="false" data-r-small="2" data-r-small-nav="true"
			 data-r-small-dots="false" data-r-medium="2" data-r-medium-nav="true" data-r-medium-dots="false" data-r-large="3" data-r-large-nav="true" data-r-large-dots="false" data-r-extra-large="3" data-r-extra-large-nav="true" data-r-extra-large-dots="false">
				@foreach($teamMembers as $member)
					<div class="col-md-4">
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-{{ 100 + $loop->index * 100 }}">
							<div class="team-box-layout1">
								<div class="maks-item animted-bg-wrap">
									<span class="animted-bg"></span>
									<div class="item-figure">
										<img src="{{ $member->photo ? asset($member->photo) : asset('media/team/team1.jpg') }}" alt="{{ $member->name }}">
									</div>
									<ul class="social-items">
										@if($member->facebook_url)<li><a href="{{ $member->facebook_url }}"><i class="fab fa-facebook-f"></i></a></li>@endif
										@if($member->twitter_url)<li><a href="{{ $member->twitter_url }}"><i class="fab fa-twitter"></i></a></li>@endif
										@if($member->instagram_url)<li><a href="{{ $member->instagram_url }}"><i class="fab fa-instagram"></i></a></li>@endif
										@if($member->linkedin_url)<li><a href="{{ $member->linkedin_url }}"><i class="fab fa-linkedin-in"></i></a></li>@endif
									</ul>
								</div>
								<div class="item-content">
									<h3 class="item-title"><a href="{{ route('team') }}">{{ $member->name }}</a></h3>
									<div class="sub-title">{{ $member->position }}</div>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>
	@endif

	{{-- ===== Brand carousel ===== --}}
	@include('partials.brand-carousel')
@endsection
