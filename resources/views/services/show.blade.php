@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $service->title, 'breadcrumbParent' => 'Service'])

	<section class="service-details-wrap section-padding-md-equal bg-color-accent2">
		<div class="container">
			<div class="row mb--60 d-flex align-items-center">
				<div class="col-lg-6 has-animation pr-5">
					<div class="service-details-box">
						<div class="content-holder">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<div class="icon-holder icon-color-emerald">
									<i class="{{ $service->icon ?: 'flaticon-growth-1' }}"></i>
								</div>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-200">
								<h2 class="item-title">{{ $service->title }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<div class="sub-paragraph">{{ $service->short_description }}</div>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
								<div class="service-details-content">{!! $service->content !!}</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 has-animation">
					<div class="service-details-box">
						<div class="item-figure">
							<div class="translate-right-75 opacity-animation transition-150 transition-delay-100">
								<img src="{{ $service->image ? asset($service->image) : asset('media/illustration/illustration19.png') }}" alt="{{ $service->title }}">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row box-bottom-margin has-animation">
				@php
					$features = $service->features ?: \App\Models\Service::defaultFeatures();
				@endphp
				@foreach($features as $i => $feature)
					<div class="col-xl-3 col-sm-6 col-12">
						<div class="service-details-box">
							<div class="translate-zoomout-50 opacity-animation transition-150 transition-delay-{{ 100 + $i * 100 }}">
								<div class="feature-item {{ $feature['color'] }}">
									<div class="feature-icon"><i class="{{ $feature['icon'] }}"></i></div>
									<div class="feature-number">{{ $i + 1 }}</div>
									<h3 class="feature-title">{{ $feature['title'] }}</h3>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			@if($otherServices->isNotEmpty())
				<div class="row d-flex align-items-center has-animation">
					<div class="col-lg-6 has-animation order-lg-2">
						<div class="service-details-box">
							<div class="content-holder">
								<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
									<h2 class="item-title">{{ $siteSettings->services_explore_heading ?: 'Explore Our Other Services' }}</h2>
								</div>
								<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-200">
									<ul class="list-item">
										@foreach($otherServices as $other)
											<li><a href="{{ route('services.show', $other->slug) }}">{{ $other->title }}</a></li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 has-animation order-lg-1">
						<div class="service-details-box">
							<div class="item-figure">
								<div class="translate-left-75 opacity-animation transition-150 transition-delay-100">
									<img src="{{ $siteSettings->services_explore_image ? asset($siteSettings->services_explore_image) : asset('media/illustration/illustration20.png') }}" alt="Service">
								</div>
							</div>
						</div>
					</div>
				</div>
			@endif
		</div>
	</section>

	@include('partials.brand-carousel', ['bgClass' => 'bg-color-accent5', 'autoplay' => true, 'rXMedium' => 2, 'rSmall' => 3])
@endsection
