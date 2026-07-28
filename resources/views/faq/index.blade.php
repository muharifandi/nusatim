@extends('layouts.app')

@section('content')

	@push('structured-data')
	<script type="application/ld+json">
	{
		"@@context": "https://schema.org",
		"@@type": "FAQPage",
		"mainEntity": [
			@foreach($faqs as $faq)
			{
				"@@type": "Question",
				"name": {!! json_encode($faq->question) !!},
				"acceptedAnswer": {
					"@@type": "Answer",
					"text": {!! json_encode(strip_tags($faq->answer)) !!}
				}
			}@if(!$loop->last),@endif
			@endforeach
		]
	}
	</script>
	@endpush

	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Faq')])

	<section class="faq-wrap bg-color-light">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 order-lg-2">
					<h2 class="inner-section-heading">{{ $page?->field('heading', 'Frequently Asked Questions') }}</h2>
					<div class="faq-box">
						<div id="accordion">
							@forelse($faqs as $faq)
								<div class="card single-item">
									<div class="card-header item-nav">
										<a class="{{ $loop->first ? '' : 'collapsed' }}" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $faq->id }}">{{ $faq->question }}</a>
									</div>
									<div id="collapse{{ $faq->id }}" class="collapse item-content-wrap {{ $loop->first ? 'show' : '' }}" data-parent="#accordion">
										<div class="card-body item-content">
											{!! $faq->answer !!}
										</div>
									</div>
								</div>
							@empty
								<p>Belum ada pertanyaan yang dipublikasikan.</p>
							@endforelse
						</div>
					</div>
				</div>
				@if($recentPosts->isNotEmpty())
					<div class="col-lg-4 col-12 order-lg-1 template-sidebar">
						<div class="widget widget-padding bg-color-accent2">
							<div class="widget-section-heading heading-layout1">
								<h3 class="item-heading">Popular Articles</h3>
							</div>
							<div class="widget-article">
								<ul class="list-item">
									@foreach($recentPosts as $post)
										<li><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></li>
									@endforeach
								</ul>
							</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</section>

	@include('partials.brand-carousel')
@endsection
