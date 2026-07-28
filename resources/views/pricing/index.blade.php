@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Our Pricing')])

	<section class="pricing-wrap-layout3 bg-color-accent5">
		<div class="container">
			<div class="section-heading heading-dark heading-layout1">
				<h2 class="heading-main-title">{{ $page?->field('heading', 'Affordable Pricing') }}</h2>
				<p class="heading-paragraph">{{ $page?->field('heading_text') }}</p>
			</div>
			<div class="row gutters-2 justify-content-center">
				@forelse($plans as $plan)
					<div class="col-lg-3 col-sm-6 col-12">
						<div class="pricing-box-layout2">
							@if($plan->is_highlighted)
								<div class="status-shape" style="border-top-color: {{ $plan->highlight_color ?: '#5a49f8' }}"><span class="status-text">Popular</span></div>
							@endif
							<div class="item-price">
								<span class="currency">{{ $plan->currency }}</span> {{ number_format((float) $plan->price, 0, ',', '.') }}
							</div>
							<h3 class="item-title">{{ $plan->name }}</h3>
							<ul class="block-list">
								@foreach((array) $plan->features as $feature)
									<li>{{ $feature }}</li>
								@endforeach
							</ul>
							<a href="{{ $plan->cta_url ?: route('contact') }}" class="item-btn btn-ghost">{{ $plan->cta_text }}</a>
						</div>
					</div>
				@empty
					<div class="col-12 text-center">
						<p>Belum ada paket harga yang dipublikasikan.</p>
					</div>
				@endforelse
			</div>
		</div>
	</section>

	@include('partials.brand-carousel')
@endsection
