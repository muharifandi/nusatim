@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Our Services'), 'breadcrumbParent' => 'Services'])

	@php
		$colors = ['california', 'emerald', 'royal-blue', 'dodger-blue', 'sunset-orange', 'turquoise'];
	@endphp

	{{-- ===== Service grid ===== --}}
	<section class="service-wrap-layout6 section-padding-md bg-color-light">
		<div class="container">
			<div class="section-heading heading-dark heading-layout1">
				<h2 class="heading-main-title">{{ $page?->field('heading', 'Our Services') }}</h2>
				<p class="heading-paragraph">{{ $page?->field('heading_text') }}</p>
			</div>
			<div class="row">
				@forelse($services as $service)
					<div class="col-lg-4 col-sm-6 col-12 has-animation">
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
							<div class="service-box-layout3">
								<div class="item-icon {{ $colors[$loop->index % count($colors)] }}">
									<i class="{{ $service->icon ?: 'flaticon-shout' }}"></i>
								</div>
								<div class="item-content">
									<h3 class="item-title"><a href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a></h3>
									<p>{{ $service->short_description }}</p>
									<a href="{{ route('services.show', $service->slug) }}" class="btn-text">Read More<i class="flaticon-next"></i></a>
								</div>
							</div>
						</div>
					</div>
				@empty
					<div class="col-12 text-center">
						<p>Belum ada layanan yang dipublikasikan.</p>
					</div>
				@endforelse
			</div>
		</div>
	</section>

	{{-- ===== Stats ===== --}}
	<section class="progress-wrap-layout1 bg-gradient-layout2">
		<div class="progress-inner-wrap bg-position-center bg-no-repeat bg-size-cover parallaxie" data-bg-image="{{ asset('media/element/element1.png') }}">
			<div class="container zindex-level-2">
				<div class="row">
					@for($i = 1; $i <= 4; $i++)
						<div class="col-xl-3 col-sm-6 col-12">
							<div class="progress-box-layout1">
								<h2 class="counting-text counter" data-num="{{ $page?->field("stat_{$i}_number", 0) }}">{{ $page?->field("stat_{$i}_number", 0) }}</h2>
								<div class="item-label">{{ $page?->field("stat_{$i}_label") }}</div>
							</div>
						</div>
					@endfor
				</div>
			</div>
		</div>
	</section>

	{{-- ===== About blocks ===== --}}
	<section class="section-padding-md-equal about-wrap-layout6 overflow-hidden">
		<div class="container">
			<div class="row d-flex align-items-center mb--100">
				<div class="col-lg-6">
					<div class="about-box-layout6">
						<div class="figure-holder has-animation">
							<div class="animated-figure">
								<div class="translate-zoomout-50 opacity-animation transition-200 transition-delay-100">
									<img src="{{ $page?->field('about_1_image') ? asset($page->field('about_1_image')) : asset('media/illustration/illustration17.png') }}" alt="About">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-box-layout6">
						<div class="content-holder has-animation">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<h2 class="item-title">{{ $page?->field('about_1_title') }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<p>{{ $page?->field('about_1_text') }}</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
								<a href="{{ route('about') }}" class="btn-fill btn-gradient">Read More<i class="flaticon-next"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row d-flex align-items-center">
				<div class="col-lg-6 order-lg-2">
					<div class="about-box-layout6">
						<div class="figure-holder has-animation">
							<div class="animated-figure">
								<div class="translate-zoomout-50 opacity-animation transition-200 transition-delay-100">
									<img src="{{ $page?->field('about_2_image') ? asset($page->field('about_2_image')) : asset('media/illustration/illustration18.png') }}" alt="About">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 order-lg-1">
					<div class="about-box-layout6">
						<div class="content-holder has-animation">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<h2 class="item-title">{{ $page?->field('about_2_title') }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<p>{{ $page?->field('about_2_text') }}</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
								<a href="{{ route('about') }}" class="btn-fill btn-gradient">Read More<i class="flaticon-next"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ===== Brand carousel ===== --}}
	@include('partials.brand-carousel')
@endsection
