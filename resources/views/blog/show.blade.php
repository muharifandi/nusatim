@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/css/blog-modern.css') }}">
@endpush

@section('content')

	@push('structured-data')
	<script type="application/ld+json">
	{
		"@@context": "https://schema.org",
		"@@type": "BlogPosting",
		"headline": "{{ $post->title }}",
		"image": "{{ $post->featured_image ? asset($post->featured_image) : '' }}",
		"datePublished": "{{ optional($post->published_at)->toAtomString() }}",
		"author": { "@@type": "Person", "name": "{{ $post->author_name }}" },
		"publisher": { "@@type": "Organization", "name": "{{ $siteSettings->company_name }}" }
	}
	</script>
	@endpush

	@php
		$shareUrl = urlencode(request()->url());
		$shareTitle = urlencode($post->title);
		$readMinutes = max(1, (int) ceil(str_word_count(strip_tags($post->content ?? '')) / 200));
		$authorName = $post->author_name ?: $siteSettings->company_name;
	@endphp

	<div class="blog-modern bm-article">

		<div class="bm-article-header">
			<div class="bm-container-narrow">
				<a href="{{ route('blog.index') }}" class="bm-back"><i class="flaticon-back"></i> Kembali ke Blog</a>
				@if($post->category)<span class="bm-eyebrow">{{ $post->category }}</span>@endif
				<h1>{{ $post->title }}</h1>
				@if($post->excerpt)<p class="bm-excerpt">{{ $post->excerpt }}</p>@endif
				<div class="bm-byline">
					<div class="bm-avatar">{{ strtoupper(substr($authorName, 0, 1)) }}</div>
					<div>
						<div class="bm-byline-name">{{ $authorName }}</div>
						<div class="bm-byline-meta">{{ optional($post->published_at)->format('d M Y') }} &middot; {{ $readMinutes }} menit baca</div>
					</div>
					<div class="bm-share-inline">
						<a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke Facebook"><i class="fab fa-facebook-f"></i></a>
						<a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke Twitter/X"><i class="fab fa-twitter"></i></a>
						<a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke LinkedIn"><i class="fab fa-linkedin-in"></i></a>
						<a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke WhatsApp"><i class="fab fa-whatsapp"></i></a>
					</div>
				</div>
			</div>
		</div>

		@if($post->featured_image)
			<div class="bm-hero-image">
				<img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" loading="eager" fetchpriority="high">
			</div>
		@endif

		<div class="bm-container-narrow">
			<div class="bm-prose">
				{!! $post->content !!}
			</div>

			@if(! empty($post->tags))
				<div class="bm-tags">
					@foreach($post->tags as $tag)
						<a href="{{ route('blog.index', ['keyword' => $tag]) }}">{{ $tag }}</a>
					@endforeach
				</div>
			@endif

			<div class="bm-share-block">
				<span class="bm-share-label">Bagikan artikel ini:</span>
				<a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke Facebook"><i class="fab fa-facebook-f"></i></a>
				<a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke Twitter/X"><i class="fab fa-twitter"></i></a>
				<a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke LinkedIn"><i class="fab fa-linkedin-in"></i></a>
				<a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Share ke WhatsApp"><i class="fab fa-whatsapp"></i></a>
			</div>

			@if($post->author_name)
				<div class="bm-author-card">
					<div class="bm-avatar">{{ strtoupper(substr($authorName, 0, 1)) }}</div>
					<div>
						<div class="bm-author-card-name">{{ $authorName }}</div>
						<p class="bm-author-card-bio">{{ $siteSettings->tagline }}</p>
					</div>
				</div>
			@endif
		</div>

		@if($recentPosts->isNotEmpty())
			<div class="bm-related">
				<div class="bm-container">
					<h2>Artikel Lainnya</h2>
					<div class="bm-grid">
						@foreach($recentPosts as $related)
							<a href="{{ route('blog.show', $related->slug) }}" class="bm-card">
								<div class="bm-card-image">
									<img src="{{ $related->featured_image ? asset($related->featured_image) : asset('media/blog/blog4.jpg') }}" alt="{{ $related->title }}" loading="lazy" decoding="async">
								</div>
								@if($related->category)<span class="bm-card-category">{{ $related->category }}</span>@endif
								<h3>{{ $related->title }}</h3>
								<span class="bm-card-date">{{ optional($related->published_at)->format('d M Y') }}</span>
							</a>
						@endforeach
					</div>
				</div>
			</div>
		@endif

	</div>

	@include('partials.brand-carousel')
@endsection
