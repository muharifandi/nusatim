@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/css/modern-home.css') }}">
@endpush

@section('content')

	@push('structured-data')
	<script type="application/ld+json">
	{
		"@@context": "https://schema.org",
		"@@type": "Organization",
		"name": "{{ $siteSettings->company_name }}",
		"legalName": "{{ $siteSettings->legal_name }}",
		"url": "{{ url('/') }}",
		"logo": "{{ $siteSettings->logo_light ? asset($siteSettings->logo_light) : '' }}",
		"description": "{{ $siteSettings->default_meta_description }}"
	}
	</script>
	@endpush

	<div class="modern-home">

		{{-- ===== Hero ===== --}}
		<section class="m-hero">
			<div class="m-container">
				<div class="m-hero-grid">
					<div class="m-hero-content">
						<span class="m-eyebrow">{{ $page?->field('hero_eyebrow', 'Technology & Digital Solutions') }}</span>
						<h1 class="m-heading m-hero-heading">
							{{ $page?->field('hero_title_line1', 'Kami bantu bisnis Anda') }}
							<span class="m-accent">{{ $page?->field('hero_title_accent', 'tumbuh lewat teknologi') }}</span>
						</h1>
						<p class="m-text">{{ $page?->field('hero_text') }}</p>
						<div class="m-hero-ctas">
							<a href="{{ $page?->field('hero_cta_url', route('contact')) }}" class="m-btn m-btn-primary">{{ $page?->field('hero_cta_text', 'Mulai Konsultasi') }}</a>
							<a href="{{ route('services.index') }}" class="m-btn m-btn-ghost">{{ $page?->field('hero_secondary_cta_text', 'Lihat Layanan') }}</a>
						</div>
						<div class="m-risk-reversal"><i class="fas fa-check-circle"></i> {{ $page?->field('hero_risk_reversal', 'Konsultasi awal gratis, tanpa komitmen') }}</div>
						@if($testimonials->isNotEmpty())
							<div class="m-trust">
								<div class="m-trust-avatars">
									@foreach($testimonials->take(4) as $testimonial)
										<img src="{{ $testimonial->photo ? asset($testimonial->photo) : asset('media/testimonial/testimonial1.jpg') }}" alt="{{ $testimonial->name }}">
									@endforeach
								</div>
								<div class="m-trust-text"><strong>{{ $page?->field('trust_highlight', '50+') }}</strong> {{ $page?->field('trust_text', 'bisnis sudah mempercayai kami') }}</div>
							</div>
						@endif
					</div>
					<div class="m-hero-visual">
						<div class="m-hero-card">
							<div class="m-hero-card-row">
								<span class="m-hero-card-dot"></span>
								<span class="m-hero-card-dot"></span>
								<span class="m-hero-card-dot"></span>
							</div>
							<div class="m-hero-card-bars">
								<span style="height: 45%"></span>
								<span style="height: 70%"></span>
								<span style="height: 55%"></span>
								<span style="height: 90%"></span>
								<span style="height: 65%"></span>
								<span style="height: 80%"></span>
							</div>
							<div class="m-hero-card-label">
								<div class="m-title">{{ $page?->field('hero_card_title', 'Project Growth') }}</div>
								<div class="m-sub">{{ $page?->field('hero_card_subtitle', 'Rata-rata peningkatan 40% dalam 6 bulan') }}</div>
							</div>
						</div>
						<div class="m-float-badge m-float-badge-1">
							<div class="m-icon-circle" style="margin:0"><i class="flaticon-goal"></i></div>
							<div>
								<div class="m-num">{{ $page?->field('hero_badge_1_number', '98%') }}</div>
								<div class="m-label">{{ $page?->field('hero_badge_1_label', 'Kepuasan Klien') }}</div>
							</div>
						</div>
						<div class="m-float-badge m-float-badge-2">
							<div class="m-icon-circle" style="margin:0"><i class="flaticon-support"></i></div>
							<div>
								<div class="m-num">{{ $page?->field('hero_badge_2_number', '24/7') }}</div>
								<div class="m-label">{{ $page?->field('hero_badge_2_label', 'Dukungan Teknis') }}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		{{-- ===== Quick picks ===== --}}
		<div class="m-container">
			<div class="m-quick-picks">
				<a href="{{ route('contact') }}" class="m-quick-pick m-quick-pick-primary">
					<div>
						<div class="m-title">{{ $page?->field('quick_pick_1_title', 'Butuh Konsultasi?') }}</div>
						<div class="m-sub">{{ $page?->field('quick_pick_1_text', 'Diskusikan kebutuhan teknologi bisnis Anda, gratis') }}</div>
					</div>
					<span class="m-arrow"><i class="flaticon-next"></i></span>
				</a>
				<a href="{{ route('portfolio') }}" class="m-quick-pick m-quick-pick-light">
					<div>
						<div class="m-title">{{ $page?->field('quick_pick_2_title', 'Sudah Punya Proyek?') }}</div>
						<div class="m-sub">{{ $page?->field('quick_pick_2_text', 'Lihat portofolio dan hasil kerja tim kami') }}</div>
					</div>
					<span class="m-arrow"><i class="flaticon-next"></i></span>
				</a>
			</div>
		</div>

		{{-- ===== Logo cloud ===== --}}
		@if($clients->isNotEmpty())
		<section class="m-logos">
			<div class="m-container">
				<div class="m-logos-label">{{ $page?->field('logos_label', 'Dipercaya oleh perusahaan-perusahaan berikut') }}</div>
				<div class="m-logos-row">
					@foreach($clients as $client)
						<a href="{{ $client->website_url ?: '#' }}"><img src="{{ asset($client->logo) }}" alt="{{ $client->name }}"></a>
					@endforeach
				</div>
			</div>
		</section>
		@endif

		{{-- ===== Services ===== --}}
		<section class="m-section">
			<div class="m-container">
				<div class="m-section-head">
					<span class="m-eyebrow">{{ $page?->field('services_eyebrow', 'Layanan Kami') }}</span>
					<h2 class="m-heading">{{ $page?->field('services_heading', 'Solusi Teknologi yang Kami Tawarkan') }}</h2>
					<p class="m-text">{{ $page?->field('services_text', 'Dari konsultasi hingga eksekusi, kami mendampingi bisnis Anda di setiap tahap.') }}</p>
				</div>
				<div class="m-services-grid">
					@foreach($services as $service)
						<div class="m-service-card">
							<div class="m-icon-circle"><i class="{{ $service->icon ?: 'flaticon-shout' }}"></i></div>
							<h3>{{ $service->title }}</h3>
							<p>{{ $service->short_description }}</p>
							<a href="{{ route('services.show', $service->slug) }}" class="m-service-link">{{ $page?->field('services_link_text', 'Pelajari lebih lanjut') }} <i class="flaticon-next"></i></a>
						</div>
					@endforeach
				</div>
			</div>
		</section>

		{{-- ===== Why Us (dark) ===== --}}
		<section class="m-section">
			<div class="m-container">
				<div class="m-why-us">
					<div class="m-section-head">
						<span class="m-eyebrow">{{ $page?->field('why_us_eyebrow', 'Kenapa Nusatim') }}</span>
						<h2 class="m-heading">{{ $page?->field('why_us_heading', 'Keunggulan yang Membedakan Kami') }}</h2>
						<p class="m-text">{{ $page?->field('why_us_text', 'Bukan sekadar vendor - kami mitra teknologi jangka panjang untuk pertumbuhan bisnis Anda.') }}</p>
					</div>
					<div class="m-why-grid">
						<div class="m-why-card">
							<div class="m-icon-circle"><i class="{{ $page?->field('why_us_1_icon', 'flaticon-growth-1') }}"></i></div>
							<h3>{{ $page?->field('why_us_1_title', 'Tim Ahli & Berpengalaman') }}</h3>
							<p>{{ $page?->field('why_us_1_text', 'Dikerjakan langsung oleh konsultan dan developer yang sudah menangani berbagai skala proyek.') }}</p>
						</div>
						<div class="m-why-card">
							<div class="m-icon-circle"><i class="{{ $page?->field('why_us_2_icon', 'flaticon-marketing') }}"></i></div>
							<h3>{{ $page?->field('why_us_2_title', 'Solusi Sesuai Kebutuhan') }}</h3>
							<p>{{ $page?->field('why_us_2_text', 'Tidak ada solusi generik - setiap proyek dirancang sesuai tujuan bisnis spesifik Anda.') }}</p>
						</div>
						<div class="m-why-card">
							<div class="m-icon-circle"><i class="{{ $page?->field('why_us_3_icon', 'flaticon-support') }}"></i></div>
							<h3>{{ $page?->field('why_us_3_title', 'Dukungan Berkelanjutan') }}</h3>
							<p>{{ $page?->field('why_us_3_text', 'Hubungan tidak berhenti saat proyek selesai - kami tetap mendampingi setelah go-live.') }}</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		{{-- ===== Stats ===== --}}
		<section class="m-section m-section-soft">
			<div class="m-container">
				<div class="m-stats">
					<div class="m-stat">
						<div class="m-num">{{ $page?->field('stat_1_number', '16') }}</div>
						<div class="m-label">{{ $page?->field('stat_1_label', 'Global Customer') }}</div>
					</div>
					<div class="m-stat">
						<div class="m-num">{{ $page?->field('stat_2_number', '481') }}</div>
						<div class="m-label">{{ $page?->field('stat_2_label', 'Completed Projects') }}</div>
					</div>
					<div class="m-stat">
						<div class="m-num">{{ $page?->field('stat_3_number', '165') }}</div>
						<div class="m-label">{{ $page?->field('stat_3_label', 'Experts Worker') }}</div>
					</div>
					<div class="m-stat">
						<div class="m-num">{{ $page?->field('stat_4_number', '98%') }}</div>
						<div class="m-label">{{ $page?->field('stat_4_label', 'Client Satisfaction') }}</div>
					</div>
				</div>
			</div>
		</section>

		{{-- ===== About ===== --}}
		<section class="m-section">
			<div class="m-container">
				<div class="m-about-grid">
					<div>
						<span class="m-eyebrow">{{ $page?->field('about_eyebrow', 'Tentang Kami') }}</span>
						<h2 class="m-heading">{{ $page?->field('about_heading', 'Mitra Teknologi yang Bisa Anda Percaya') }}</h2>
						<p class="m-text">{{ $page?->field('about_text') }}</p>
						<ul class="m-check-list">
							@foreach(explode('|', (string) $page?->field('about_checklist', 'Tim Berpengalaman|Harga Transparan|Dukungan Responsif|Solusi Sesuai Kebutuhan')) as $item)
								@if(trim($item) !== '')
									<li><i class="fas fa-check-circle"></i> {{ trim($item) }}</li>
								@endif
							@endforeach
						</ul>
						<div style="margin-top: 32px;">
							<a href="{{ route('about') }}" class="m-btn m-btn-primary">{{ $page?->field('about_button_text', 'Selengkapnya Tentang Kami') }}</a>
						</div>
					</div>
					<div class="m-about-visual">
						<img src="{{ $page?->field('about_image') ? asset($page->field('about_image')) : asset('media/team/team1.jpg') }}" alt="{{ $siteSettings->company_name }}" class="m-about-photo">
					</div>
				</div>
			</div>
		</section>

		{{-- ===== Testimonials ===== --}}
		@if($testimonials->isNotEmpty())
		<section class="m-section m-section-soft">
			<div class="m-container">
				<div class="m-section-head">
					<span class="m-eyebrow">{{ $page?->field('testimonials_eyebrow', 'Testimoni') }}</span>
					<h2 class="m-heading">{{ $page?->field('testimonials_heading', 'Apa Kata Klien Kami') }}</h2>
				</div>
				<div class="m-testimonial-grid">
					@foreach($testimonials->take(3) as $testimonial)
						<div class="m-testimonial-card">
							<div class="m-stars">
								@for($i = 0; $i < $testimonial->rating; $i++)<i class="fas fa-star"></i>@endfor
							</div>
							<p>&ldquo;{{ $testimonial->quote }}&rdquo;</p>
							<div class="m-testimonial-author">
								<img src="{{ $testimonial->photo ? asset($testimonial->photo) : asset('media/testimonial/testimonial1.jpg') }}" alt="{{ $testimonial->name }}">
								<div>
									<div class="m-name">{{ $testimonial->name }}</div>
									<div class="m-role">{{ $testimonial->position }}</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</section>
		@endif

		{{-- ===== Portfolio preview ===== --}}
		@if($projects->isNotEmpty())
		<section class="m-section">
			<div class="m-container">
				<div class="m-section-head">
					<span class="m-eyebrow">{{ $page?->field('portfolio_eyebrow', 'Portfolio') }}</span>
					<h2 class="m-heading">{{ $page?->field('portfolio_heading', 'Proyek yang Telah Kami Kerjakan') }}</h2>
					<p class="m-text">{{ $page?->field('portfolio_text', 'Sebagian kecil hasil kerja tim kami untuk klien.') }}</p>
				</div>
				<div class="m-card-grid">
					@foreach($projects as $project)
						<a href="{{ route('portfolio.show', $project->slug) }}" class="m-media-card">
							<div class="m-media-figure">
								<img src="{{ asset($project->image) }}" alt="{{ $project->title }}">
							</div>
							<div class="m-media-body">
								@if($project->category)<span class="m-media-tag">{{ $project->category }}</span>@endif
								<h3>{{ $project->title }}</h3>
							</div>
						</a>
					@endforeach
				</div>
				<div class="text-center" style="text-align:center; margin-top: 40px;">
					<a href="{{ route('portfolio') }}" class="m-btn m-btn-ghost">{{ $page?->field('portfolio_button_text', 'Lihat Semua Portfolio') }}</a>
				</div>
			</div>
		</section>
		@endif

		{{-- ===== Blog preview ===== --}}
		@if($posts->isNotEmpty())
		<section class="m-section m-section-soft">
			<div class="m-container">
				<div class="m-section-head">
					<span class="m-eyebrow">{{ $page?->field('blog_eyebrow', 'Blog') }}</span>
					<h2 class="m-heading">{{ $page?->field('latest_news_section_heading', 'Wawasan Terbaru') }}</h2>
					<p class="m-text">{{ $page?->field('latest_news_section_text', 'Tips dan update seputar teknologi serta digital marketing.') }}</p>
				</div>
				<div class="m-card-grid">
					@foreach($posts as $post)
						<a href="{{ route('blog.show', $post->slug) }}" class="m-media-card">
							<div class="m-media-figure">
								<img src="{{ $post->featured_image ? asset($post->featured_image) : asset('media/blog/blog4.jpg') }}" alt="{{ $post->title }}">
							</div>
							<div class="m-media-body">
								<span class="m-media-tag">{{ optional($post->published_at)->format('d M Y') }}</span>
								<h3>{{ $post->title }}</h3>
								<p>{{ $post->excerpt }}</p>
							</div>
						</a>
					@endforeach
				</div>
			</div>
		</section>
		@endif

		{{-- ===== CTA ===== --}}
		<section class="m-section">
			<div class="m-container">
				<div class="m-cta">
					<div class="m-cta-inner">
						<h2>{{ $page?->field('cta_heading', 'Siap Mengembangkan Bisnis Anda?') }}</h2>
						<p>{{ $page?->field('cta_text', 'Konsultasikan kebutuhan teknologi dan digital marketing Anda bersama tim kami, gratis.') }}</p>
						<a href="{{ $page?->field('cta_button_url', route('contact')) }}" class="m-btn m-btn-primary">{{ $page?->field('cta_button_text', 'Hubungi Kami Sekarang') }}</a>
					</div>
				</div>
			</div>
		</section>

	</div>

@endsection
