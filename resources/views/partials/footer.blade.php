<!--=====================================-->
<!--=     Footer Section Area Start     =-->
<!--=====================================-->
<footer id="footer-wrap-layout1" class="footer-wrap-layout1 bg-color-light">
	<div class="footer-top-layout1 bg-no-repeat bg-size-cover bg-position-center" data-bg-image="{{ asset('media/element/element3.png') }}">
		<div class="container">
			<div class="row">
				<div class="col-xl-3 col-lg-3 col-md-6 col-12">
					<div class="footer-widgets">
						<div class="footer-logo">
							<a href="{{ route('home') }}"><img src="{{ ($siteSettings->logo_footer ?: $siteSettings->logo_dark) ? asset($siteSettings->logo_footer ?: $siteSettings->logo_dark) : asset('media/logo.png') }}" alt="{{ $siteSettings->company_name }}"></a>
						</div>
						<p class="about-paragraph">{{ $siteSettings->tagline }}</p>
						<ul class="footer-social">
							@if($siteSettings->facebook_url)<li><a href="{{ $siteSettings->facebook_url }}"><i class="fab fa-facebook-f"></i></a></li>@endif
							@if($siteSettings->twitter_url)<li><a href="{{ $siteSettings->twitter_url }}"><i class="fab fa-twitter"></i></a></li>@endif
							@if($siteSettings->instagram_url)<li><a href="{{ $siteSettings->instagram_url }}"><i class="fab fa-instagram"></i></a></li>@endif
							@if($siteSettings->linkedin_url)<li><a href="{{ $siteSettings->linkedin_url }}"><i class="fab fa-linkedin-in"></i></a></li>@endif
							@if($siteSettings->youtube_url)<li><a href="{{ $siteSettings->youtube_url }}"><i class="fab fa-youtube"></i></a></li>@endif
						</ul>
					</div>
				</div>
				@foreach($footerMenu?->items ?? [] as $column)
					<div class="col-xl-3 col-lg-3 col-md-6 col-12">
						<div class="footer-widgets">
							<h3 class="footer-widget-heading">{{ $column->label }}</h3>
							<ul class="footer-menu">
								@foreach($column->children as $link)
									<li><a href="{{ $link->url ?: '#' }}" target="{{ $link->target }}">{{ $link->label }}</a></li>
								@endforeach
							</ul>
						</div>
					</div>
				@endforeach
				<div class="col-xl-3 col-lg-3 col-md-6 col-12">
					<div class="footer-widgets">
						<h3 class="footer-widget-heading">Contact Info</h3>
						<p>{{ $siteSettings->tagline }}</p>
						<ul class="footer-contact">
							@if($siteSettings->address)<li><i class="flaticon-placeholder"></i>{{ $siteSettings->address }}</li>@endif
							@if($siteSettings->email)<li><i class="flaticon-plane"></i>{{ $siteSettings->email }}</li>@endif
							@if($siteSettings->phone)<li><i class="flaticon-telephone"></i>{{ $siteSettings->phone }}</li>@endif
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom-layout1 bg-color-accent">
		<div class="container">
			<div class="copy-right-wrap">
				<p class="copy-right-text">&copy; {{ now()->year }} {{ $siteSettings->company_name }}. All Rights Reserved</p>
			</div>
		</div>
	</div>
</footer>
<!--=====================================-->
<!--=      Footer Section Area End      =-->
<!--=====================================-->
