@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Meet The Team')])

	<section id="team-wrap-layout1" class="team-wrap-layout1 section-padding-md">
		<div class="container">
			<div class="section-heading heading-dark heading-layout1">
				<h2 class="heading-main-title">{{ $page?->field('heading', 'Dedicated Team') }}</h2>
				<p class="heading-paragraph">{{ $page?->field('heading_text') }}</p>
			</div>
			<div class="row">
				@forelse($members as $member)
					<div class="col-lg-4 col-sm-6 col-12 has-animation">
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-{{ 100 + ($loop->index % 3) * 100 }}">
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
									<h3 class="item-title">{{ $member->name }}</h3>
									<div class="sub-title">{{ $member->position }}</div>
								</div>
							</div>
						</div>
					</div>
				@empty
					<div class="col-12 text-center">
						<p>Belum ada anggota tim yang dipublikasikan.</p>
					</div>
				@endforelse
			</div>
		</div>
	</section>

	@include('partials.brand-carousel')
@endsection
