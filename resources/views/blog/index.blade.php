@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/css/blog-modern.css') }}?v={{ filemtime(public_path('assets/css/blog-modern.css')) }}">
@endpush

@section('content')

	<div class="blog-modern">

		<section class="bm-header">
			<div class="bm-container bm-header-grid">
				<div class="bm-header-copy">
					<span class="bm-eyebrow-pill">Blog &amp; Insight</span>
					<h1>{{ $page?->field('heading', 'Wawasan & Update Terbaru') }}</h1>
					<p>{{ $page?->field('heading_text', 'Tips, insight, dan update seputar teknologi, pengembangan software, dan digital marketing.') }}</p>
				</div>
				<div class="bm-header-art" aria-hidden="true">
					<span class="bm-art-blob bm-art-blob-a"></span>
					<span class="bm-art-blob bm-art-blob-b"></span>

					<div class="bm-art-bubble">
						<span></span><span></span>
					</div>

					<div class="bm-art-card">
						<div class="bm-art-card-header">
							<span class="bm-art-card-header-dot"></span>
							<div class="bm-art-card-header-lines"><span></span><span></span></div>
							<div class="bm-art-card-header-menu"><span></span><span></span><span></span></div>
						</div>
						<div class="bm-art-card-body">
							<div class="bm-art-card-row">
								<div class="bm-art-card-icon"><i class="fas fa-image"></i></div>
								<div class="bm-art-card-row-lines"><span></span><span></span></div>
							</div>
							<div class="bm-art-card-lines">
								<span></span><span></span><span></span><span></span><span></span>
							</div>
						</div>
					</div>

					<div class="bm-art-bulb">
						<i class="far fa-lightbulb"></i>
						<span class="bm-art-ray bm-art-ray-1"></span>
						<span class="bm-art-ray bm-art-ray-2"></span>
						<span class="bm-art-ray bm-art-ray-3"></span>
						<span class="bm-art-ray bm-art-ray-4"></span>
						<span class="bm-art-ray bm-art-ray-5"></span>
					</div>
				</div>
			</div>
		</section>

		<section class="bm-content">
			<div class="bm-container">

				@if($featuredPost)
					@php
						$featuredReadMinutes = max(1, (int) ceil(str_word_count(strip_tags($featuredPost->content ?? '')) / 200));
						$featuredAuthorName = $featuredPost->author_name ?: $siteSettings->company_name;
					@endphp
					<a href="{{ route('blog.show', $featuredPost->slug) }}" class="bm-featured">
						<div class="bm-featured-image">
							<span class="bm-featured-tag">Featured</span>
							<img src="{{ $featuredPost->featured_image ? asset($featuredPost->featured_image) : asset('media/blog/blog4.jpg') }}" alt="{{ $featuredPost->title }}" loading="eager" fetchpriority="high">
						</div>
						<div class="bm-featured-body">
							@if($featuredPost->category)<span class="bm-featured-category">{{ $featuredPost->category }}</span>@endif
							<h2>{{ $featuredPost->title }}</h2>
							<p>{{ $featuredPost->excerpt }}</p>
							<div class="bm-featured-meta">
								<div class="bm-avatar">{{ strtoupper(substr($featuredAuthorName, 0, 1)) }}</div>
								<div>
									<div class="bm-byline-name">{{ $featuredAuthorName }}</div>
									<div class="bm-byline-meta">{{ optional($featuredPost->published_at)->format('d M Y') }} &middot; {{ $featuredReadMinutes }} menit baca</div>
								</div>
							</div>
							<span class="bm-readmore">Baca selengkapnya <i class="flaticon-next"></i></span>
						</div>
					</a>
				@endif

				<form method="GET" action="{{ route('blog.index') }}" class="bm-filter-bar">
					<div class="bm-filter-row">
						<div class="bm-search">
							<input type="text" name="keyword" placeholder="Cari artikel atau topik..." value="{{ $filters['keyword'] ?? '' }}">
							<i class="fas fa-search"></i>
						</div>
						<select name="category" class="bm-filter-select">
							<option value="">Semua Kategori</option>
							@foreach($categories as $category)
								<option value="{{ $category }}" @selected(($filters['category'] ?? null) === $category)>{{ $category }}</option>
							@endforeach
						</select>
						<div class="bm-date-filter" id="bmDateFilter">
							<button type="button" class="bm-filter-select bm-date-trigger" id="bmDateTrigger">
								<span id="bmDateTriggerLabel">Rentang Tanggal</span>
								<i class="fas fa-chevron-down"></i>
							</button>
							<div class="bm-date-popover" id="bmDatePopover">
								<label class="bm-date-field">
									<span>Dari</span>
									<input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" aria-label="Dari tanggal">
								</label>
								<label class="bm-date-field">
									<span>Sampai</span>
									<input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" aria-label="Sampai tanggal">
								</label>
							</div>
						</div>
						<select name="sort" class="bm-filter-select">
							<option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Terbaru</option>
							<option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Terlama</option>
							<option value="alphabetical" @selected(($filters['sort'] ?? '') === 'alphabetical')>A-Z</option>
						</select>
						@if(collect($filters)->filter()->isNotEmpty())
							<a href="{{ route('blog.index') }}" class="bm-filter-reset">Reset</a>
						@endif
						<button type="submit" class="bm-filter-submit">Terapkan</button>
						<div class="bm-view-toggle" id="bmViewToggle" role="group" aria-label="Tampilan daftar artikel">
							<button type="button" class="bm-view-btn" data-view="grid" aria-label="Tampilan grid" title="Grid">
								<i class="fas fa-th-large"></i>
							</button>
							<button type="button" class="bm-view-btn" data-view="list" aria-label="Tampilan list" title="List">
								<i class="fas fa-list"></i>
							</button>
						</div>
					</div>
				</form>

				@if($posts->isEmpty())
					<div class="bm-empty">
						{{ collect($filters)->filter()->isNotEmpty() ? 'Tidak ada artikel yang cocok dengan filter ini.' : 'Belum ada artikel yang dipublikasikan.' }}
					</div>
				@else
					<div class="bm-list-toolbar">
						<span class="bm-list-toolbar-label">{{ $posts->total() }} artikel</span>
					</div>

					<div class="bm-grid" id="bmPostGrid">
						@foreach($posts as $post)
							<a href="{{ route('blog.show', $post->slug) }}" class="bm-card">
								<div class="bm-card-image">
									<img src="{{ $post->featured_image ? asset($post->featured_image) : asset('media/blog/blog4.jpg') }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
								</div>
								<div class="bm-card-body">
									@if($post->category)<span class="bm-card-category">{{ $post->category }}</span>@endif
									<h3>{{ $post->title }}</h3>
									<p>{{ $post->excerpt }}</p>
									<span class="bm-card-date">{{ optional($post->published_at)->format('d M Y') }}</span>
								</div>
							</a>
						@endforeach
					</div>
				@endif

				@if($posts->hasPages())
					<div class="bm-pagination">
						{{ $posts->links('partials.blog-pagination') }}
					</div>
				@endif

			</div>
		</section>

		<section class="bm-newsletter">
			<div class="bm-container">
				<div class="bm-newsletter-card">
					<div class="bm-newsletter-info">
						<div class="bm-newsletter-icon"><i class="fas fa-envelope-open-text"></i></div>
						<div class="bm-newsletter-text">
							<h2>Dapatkan Insight Terbaru</h2>
							<p>Berlangganan newsletter kami untuk mendapatkan artikel terbaru seputar teknologi, tips bisnis, dan update produk.</p>
						</div>
					</div>
					<div class="bm-newsletter-action">
						@if(session('status'))
							<div class="bm-newsletter-status">{{ session('status') }}</div>
						@endif
						<form method="POST" action="{{ route('newsletter.subscribe') }}" class="bm-newsletter-form">
							@csrf
							<input type="hidden" name="source" value="blog">
							<input type="email" name="email" placeholder="Masukkan email Anda" value="{{ old('email') }}" required class="@error('email') is-invalid @enderror">
							<button type="submit">Berlangganan</button>
						</form>
						@error('email')
							<span class="bm-newsletter-error">{{ $message }}</span>
						@enderror
						<p class="bm-newsletter-hint">Kami tidak akan spam email Anda. Unsubscribe kapan saja.</p>
					</div>
				</div>
			</div>
		</section>

	</div>

	@include('partials.brand-carousel')

	@push('scripts')
	<script>
		(function () {
			var grid = document.getElementById('bmPostGrid');
			var toggle = document.getElementById('bmViewToggle');
			if (! grid || ! toggle) return;

			var buttons = toggle.querySelectorAll('.bm-view-btn');
			var STORAGE_KEY = 'nusatim_blog_view';

			function setView(view) {
				grid.classList.toggle('list-view', view === 'list');
				buttons.forEach(function (btn) {
					btn.classList.toggle('active', btn.dataset.view === view);
				});
				localStorage.setItem(STORAGE_KEY, view);
			}

			buttons.forEach(function (btn) {
				btn.addEventListener('click', function () {
					setView(btn.dataset.view);
				});
			});

			setView(localStorage.getItem(STORAGE_KEY) === 'list' ? 'list' : 'grid');
		})();

		(function () {
			var wrap = document.getElementById('bmDateFilter');
			var trigger = document.getElementById('bmDateTrigger');
			var popover = document.getElementById('bmDatePopover');
			var label = document.getElementById('bmDateTriggerLabel');
			if (! wrap || ! trigger || ! popover || ! label) return;

			var startInput = popover.querySelector('input[name="start_date"]');
			var endInput = popover.querySelector('input[name="end_date"]');

			function formatDate(value) {
				if (! value) return null;
				var date = new Date(value + 'T00:00:00');
				if (isNaN(date.getTime())) return null;
				return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
			}

			function updateLabel() {
				var start = formatDate(startInput.value);
				var end = formatDate(endInput.value);
				if (start && end) {
					label.textContent = start + ' - ' + end;
				} else if (start) {
					label.textContent = 'Sejak ' + start;
				} else if (end) {
					label.textContent = 'Sampai ' + end;
				} else {
					label.textContent = 'Rentang Tanggal';
				}
				wrap.classList.toggle('has-value', !! (start || end));
			}

			trigger.addEventListener('click', function (e) {
				e.stopPropagation();
				wrap.classList.toggle('open');
			});

			document.addEventListener('click', function (e) {
				if (! wrap.contains(e.target)) {
					wrap.classList.remove('open');
				}
			});

			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape') {
					wrap.classList.remove('open');
				}
			});

			startInput.addEventListener('change', updateLabel);
			endInput.addEventListener('change', updateLabel);

			updateLabel();
		})();
	</script>
	@endpush
@endsection
