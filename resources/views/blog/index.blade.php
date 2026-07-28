@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/css/blog-modern.css') }}">
@endpush

@section('content')

	<div class="blog-modern">

		<section class="bm-header">
			<div class="bm-container">
				<span class="bm-eyebrow">Blog</span>
				<h1>{{ $page?->field('heading', 'Wawasan & Update Terbaru') }}</h1>
				<p>{{ $page?->field('heading_text', 'Tips, insight, dan update seputar teknologi, pengembangan software, dan digital marketing.') }}</p>
			</div>
		</section>

		<section class="bm-content">
			<div class="bm-container">

				@if($featuredPost)
					<a href="{{ route('blog.show', $featuredPost->slug) }}" class="bm-featured">
						<div class="bm-featured-image">
							<img src="{{ $featuredPost->featured_image ? asset($featuredPost->featured_image) : asset('media/blog/blog4.jpg') }}" alt="{{ $featuredPost->title }}" loading="eager" fetchpriority="high">
						</div>
						<div class="bm-featured-body">
							<span class="bm-featured-tag">Featured</span>
							<div class="bm-meta">
								@if($featuredPost->category)<span>{{ $featuredPost->category }}</span><span class="bm-dot"></span>@endif
								<span>{{ optional($featuredPost->published_at)->format('d M Y') }}</span>
							</div>
							<h2>{{ $featuredPost->title }}</h2>
							<p>{{ $featuredPost->excerpt }}</p>
							<span class="bm-readmore">Baca selengkapnya <i class="flaticon-next"></i></span>
						</div>
					</a>
				@endif

				<form method="GET" action="{{ route('blog.index') }}" class="bm-filter-bar">
					<div class="bm-filter-row">
						<div class="bm-search">
							<input type="text" name="keyword" placeholder="Cari artikel atau keyword..." value="{{ $filters['keyword'] ?? '' }}">
						</div>
						<select name="category" class="bm-filter-select">
							<option value="">Semua Kategori</option>
							@foreach($categories as $category)
								<option value="{{ $category }}" @selected(($filters['category'] ?? null) === $category)>{{ $category }}</option>
							@endforeach
						</select>
						<input type="date" name="start_date" class="bm-filter-date" value="{{ $filters['start_date'] ?? '' }}" aria-label="Dari tanggal">
						<input type="date" name="end_date" class="bm-filter-date" value="{{ $filters['end_date'] ?? '' }}" aria-label="Sampai tanggal">
						<select name="sort" class="bm-filter-select">
							<option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Terbaru</option>
							<option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Terlama</option>
							<option value="alphabetical" @selected(($filters['sort'] ?? '') === 'alphabetical')>A-Z</option>
						</select>
						<button type="submit" class="bm-filter-submit">Terapkan</button>
						@if(collect($filters)->filter()->isNotEmpty())
							<a href="{{ route('blog.index') }}" class="bm-filter-reset">Reset</a>
						@endif
					</div>
				</form>

				@if($posts->isEmpty())
					<div class="bm-empty">
						{{ collect($filters)->filter()->isNotEmpty() ? 'Tidak ada artikel yang cocok dengan filter ini.' : 'Belum ada artikel yang dipublikasikan.' }}
					</div>
				@else
					<div class="bm-list-toolbar">
						<span class="bm-list-toolbar-label">{{ $posts->total() }} artikel</span>
						<div class="bm-view-toggle" id="bmViewToggle" role="group" aria-label="Tampilan daftar artikel">
							<button type="button" class="bm-view-btn" data-view="grid" aria-label="Tampilan grid" title="Grid">
								<i class="fas fa-th-large"></i>
							</button>
							<button type="button" class="bm-view-btn" data-view="list" aria-label="Tampilan list" title="List">
								<i class="fas fa-list"></i>
							</button>
						</div>
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
	</script>
	@endpush
@endsection
