<!--=====================================-->
<!--=         Offcanvas Start           =-->
<!--=====================================-->
<div class="offcanvas-menu-wrap" id="offcanvas-wrap" data-position="left">
	<div class="close-btn offcanvas-close"><i class="fas fa-times"></i></div>
	<div class="offcanvas-content">
		<div class="offcanvas-logo">
			<a href="{{ route('home') }}"><img src="{{ $siteSettings->logo_light ? asset($siteSettings->logo_light) : asset('media/logo.png') }}" alt="{{ $siteSettings->company_name }}"></a>
		</div>
		<nav>
			<ul class="offcanvas-nav">
				@if($headerMenu)
					@foreach($headerMenu->items as $item)
						@php
							$link = $item->url ?: optional($item->children->first())->url ?: '#';
						@endphp
						<li class="nav-item">
							<a href="{{ $link }}">{{ $item->label }}</a>
						</li>
					@endforeach
				@endif
			</ul>
		</nav>
		<div class="offcanvas-footer">
			<p>{{ $siteSettings->tagline }}</p>
			<ul class="offcanvas-social">
				@if($siteSettings->facebook_url)<li><a href="{{ $siteSettings->facebook_url }}"><i class="fab fa-facebook-f"></i></a></li>@endif
				@if($siteSettings->twitter_url)<li><a href="{{ $siteSettings->twitter_url }}"><i class="fab fa-twitter"></i></a></li>@endif
				@if($siteSettings->linkedin_url)<li><a href="{{ $siteSettings->linkedin_url }}"><i class="fab fa-linkedin-in"></i></a></li>@endif
				@if($siteSettings->instagram_url)<li><a href="{{ $siteSettings->instagram_url }}"><i class="fab fa-instagram"></i></a></li>@endif
				@if($siteSettings->youtube_url)<li><a href="{{ $siteSettings->youtube_url }}"><i class="fab fa-youtube"></i></a></li>@endif
			</ul>
		</div>
	</div>
</div>
<div class="offcanvas-mask"></div>
<!--=====================================-->
<!--=         Offcanvas End             =-->
<!--=====================================-->
