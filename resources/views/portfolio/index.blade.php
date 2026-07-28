@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Portfolio'), 'breadcrumbParent' => 'Portfolio'])

	<section class="gallery-wrap section-padding-md-equal bg-color-light">
		<div class="container">
			<div class="row">
				@forelse($projects as $project)
					<div class="col-lg-4 col-sm-6 col-12 has-animation">
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-{{ 100 + $loop->index * 50 }}">
							<div class="gallery-box-layout1">
								<div class="item-figure">
									<img src="{{ asset($project->image) }}" alt="{{ $project->title }}">
								</div>
								<div class="item-content">
									<div class="item-icon">
										<a href="{{ asset($project->image) }}" class="popup-zoom" data-fancybox-group="gallery" title=""><i class="fas fa-plus"></i></a>
									</div>
									<h3 class="item-title"><a href="{{ route('portfolio.show', $project->slug) }}">{{ $project->title }}</a></h3>
									<p>{{ $project->category }}</p>
								</div>
							</div>
						</div>
					</div>
				@empty
					<div class="col-12 text-center">
						<p>Belum ada proyek yang dipublikasikan.</p>
					</div>
				@endforelse
			</div>
		</div>
	</section>

	@include('partials.brand-carousel')
@endsection
