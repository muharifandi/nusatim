<link rel="stylesheet" href="{{ asset('assets/css/services-hero.css') }}?v={{ filemtime(public_path('assets/css/services-hero.css')) }}">

<section class="services-hero">
	<div class="container">
		<div class="services-hero-grid">
			<div class="services-hero-content">
				<span class="services-hero-badge">{{ $page?->field('hero_eyebrow', 'Layanan Kami') }}</span>
				<h1 class="services-hero-heading">{{ $page?->field('hero_heading', 'Solusi Teknologi untuk Mendorong Pertumbuhan Bisnis') }}</h1>
				<p class="services-hero-text">{{ $page?->field('hero_text', 'Kami menyediakan layanan pengembangan digital yang dirancang untuk membantu bisnis Anda lebih efisien, terhubung dengan pelanggan, dan siap berkembang di era digital.') }}</p>

				<div class="services-hero-features">
					@php
						$heroFeatureDefaults = [
							1 => ['icon' => 'flaticon-idea', 'title' => 'Solusi Custom sesuai kebutuhan'],
							2 => ['icon' => 'flaticon-team', 'title' => 'Tim Profesional & Berpengalaman'],
							3 => ['icon' => 'flaticon-shield', 'title' => 'Kualitas Terjamin & Support Berkelanjutan'],
						];
					@endphp
					@for ($i = 1; $i <= 3; $i++)
						<div class="services-hero-feature">
							<span class="services-hero-feature-icon"><i class="{{ $page?->field("hero_feature_{$i}_icon", $heroFeatureDefaults[$i]['icon']) }}"></i></span>
							<span class="services-hero-feature-title">{{ $page?->field("hero_feature_{$i}_title", $heroFeatureDefaults[$i]['title']) }}</span>
						</div>
					@endfor
				</div>
			</div>

			<div class="services-hero-visual">
				<div class="dm-window">
					<div class="dm-sidebar">
						<div class="dm-sidebar-logo">N</div>
						<div class="dm-sidebar-icons">
							<span class="dm-sidebar-icon active"></span>
							<span class="dm-sidebar-icon"></span>
							<span class="dm-sidebar-icon"></span>
							<span class="dm-sidebar-icon"></span>
							<span class="dm-sidebar-icon"></span>
						</div>
					</div>
					<div class="dm-main">
						<div class="dm-topbar">
							<div class="dm-dots"><span></span><span></span><span></span></div>
							<div class="dm-topbar-title">Dashboard</div>
							<div class="dm-topbar-actions">
								<span class="dm-round-btn"></span>
								<span class="dm-round-btn dm-round-btn-primary"></span>
							</div>
						</div>
						<div class="dm-content-grid">
							<div class="dm-col-main">
								<div class="dm-stat-row">
									<div class="dm-stat-card">
										<div class="dm-stat-label">Total Project</div>
										<div class="dm-stat-value">347</div>
										<div class="dm-mini-bar"><span style="width:78%"></span></div>
									</div>
									<div class="dm-stat-card">
										<div class="dm-stat-label">Progress</div>
										<div class="dm-stat-value">65%</div>
										<div class="dm-mini-bar"><span style="width:65%"></span></div>
									</div>
								</div>
								<div class="dm-chart-card">
									<svg viewBox="0 0 400 120" preserveAspectRatio="none" class="dm-chart-svg">
										<polyline points="0,95 55,78 110,85 165,45 220,58 275,25 330,40 385,8"></polyline>
									</svg>
									<div class="dm-chart-labels">
										<span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
									</div>
								</div>
							</div>
							<div class="dm-col-side">
								<div class="dm-stat-card dm-stat-card-tall">
									<div class="dm-stat-label">Project Growth</div>
									<div class="dm-stat-value dm-positive">+27%</div>
									<div class="dm-stat-sub">vs bulan lalu</div>
									<div class="dm-bars">
										<span style="height:28%"></span><span style="height:42%"></span><span style="height:34%"></span><span style="height:58%"></span><span style="height:48%"></span><span style="height:72%"></span><span style="height:88%"></span>
									</div>
								</div>
								<div class="dm-satisfaction-card">
									<div class="dm-stat-label dm-positive">Kepuasan Klien</div>
									<div class="dm-stat-value dm-positive">98%</div>
									<div class="dm-stat-sub">Sangat Puas <span class="dm-emoji">🙂</span></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
