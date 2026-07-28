<!--=====================================-->
<!--=            Navbar Start           =-->
<!--=====================================-->
<header class="sticky-on">
	<div id="sticky-placeholder"></div>
	<div id="navbar-wrap" class="navbar-wrap{{ str_contains($bodyClass ?? '', 'pagepiling') ? ' possition-static' : '' }}">
		<div class="navbar-layout1">
			<div class="container">
				<div class="row no-gutters d-flex align-items-center position-relative">
					<div class="col-lg-2 d-flex justify-content-start">
						<div class="temp-logo text-center">
							<a href="{{ route('home') }}" class="default-logo">
								<img src="{{ $siteSettings->logo_light ? asset($siteSettings->logo_light) : asset('media/logo-light.png') }}" alt="{{ $siteSettings->company_name }}" class="img-fluid">
							</a>
							<a href="{{ route('home') }}" class="sticky-logo">
								<img src="{{ $siteSettings->logo_dark ? asset($siteSettings->logo_dark) : asset('media/logo-dark.png') }}" alt="{{ $siteSettings->company_name }}" class="img-fluid">
							</a>
						</div>
					</div>
					<div class="col-lg-7 d-flex justify-content-end possition-static">
						<nav id="dropdown" class="template-main-menu">
							<ul>
								@if($headerMenu)
									@foreach($headerMenu->items as $item)
										@include('partials.menu-item', ['item' => $item, 'depth' => 1])
									@endforeach
								@endif
							</ul>
						</nav>
					</div>
					<div class="col-lg-3 d-flex justify-content-end">
						<ul class="header-action-items">
							<li class="single-item">
								<div class="lang-switcher" id="langSwitcher">
									<button type="button" class="lang-switcher-btn" id="langSwitcherBtn" aria-haspopup="true" aria-expanded="false" title="Pilih Bahasa">
										<span class="lang-flag">🇮🇩</span><span class="lang-code">ID</span>
										<i class="fas fa-chevron-down lang-caret"></i>
									</button>
									<ul class="lang-switcher-menu" id="langSwitcherMenu">
										<li class="active"><a href="#"><span class="lang-flag">🇮🇩</span> Indonesia</a></li>
										<li class="disabled"><span><span class="lang-flag">🇬🇧</span> English <em>Segera Hadir</em></span></li>
									</ul>
								</div>
							</li>
							<li class="single-item mr-2">
								<a href="{{ route('contact') }}" class="item-btn btn-ghost btn-light">{{ $siteSettings->nav_cta_text ?: 'Get a Quote' }}</a>
							</li>
							<li class="single-item">
								<button type="button" class="offcanvas-menu-btn menu-status-open">
									<span class="menu-btn-icon">
										<span></span>
										<span></span>
										<span></span>
									</span>
								</button>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!--=====================================-->
<!--=              Navbar End           =-->
<!--=====================================-->
