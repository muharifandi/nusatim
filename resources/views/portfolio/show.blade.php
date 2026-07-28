@extends('layouts.app')

@section('content')
	@include('partials.page-banner', ['title' => $project->title, 'breadcrumbParent' => 'Portfolio'])

	<section class="gallery-details-wrap section-padding-md-equal bg-color-light">
		<div class="container">
			<div class="mb-5">
				<img src="{{ asset($project->image) }}" alt="{{ $project->title }}" class="w-100">
			</div>
			<div class="row">
				<div class="col-lg-8">
					<div class="gallery-details-box">
						<div class="item-content">
							<h2 class="item-title">{{ $project->title }}</h2>
							<p>{{ $project->description }}</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-12 template-sidebar">
					<div class="widget bg-color-light">
						<div class="widget-info">
							<div class="item-content">
								<div class="widget-section-heading heading-layout1">
									<h3 class="item-heading">Information</h3>
								</div>
								<ul class="list-item">
									@if($project->category)<li>Category:<span>{{ $project->category }}</span></li>@endif
									<li>Date:<span>{{ $project->created_at->format('d.m.Y') }}</span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			@if($otherProjects->isNotEmpty())
			<div class="related-gallery-carousel">
				<div class="row">
					<div class="col-8">
						<div class="widget-section-heading">
							<h3 class="item-heading">Related</h3>
						</div>
					</div>
				</div>
				<div class="rc-carousel nav-control-layout3" data-loop="true" data-items="8" data-margin="30" data-autoplay="false" data-autoplay-timeout="5000" data-smart-speed="700" data-dots="false" data-nav="true" data-nav-speed="false"
				 data-r-x-small="1" data-r-x-small-nav="true" data-r-x-small-dots="false" data-r-x-medium="1" data-r-x-medium-nav="true" data-r-x-medium-dots="false" data-r-small="2" data-r-small-nav="true" data-r-small-dots="false" data-r-medium="2" data-r-medium-nav="true"
				 data-r-medium-dots="false" data-r-large="3" data-r-large-nav="true" data-r-large-dots="false" data-r-extra-large="3" data-r-extra-large-nav="true" data-r-extra-large-dots="false">
					@foreach($otherProjects as $other)
						<div class="gallery-box-layout1">
							<div class="item-figure">
								<img src="{{ asset($other->image) }}" alt="{{ $other->title }}">
							</div>
							<div class="item-content">
								<div class="item-icon">
									<a href="{{ asset($other->image) }}" class="popup-zoom" data-fancybox-group="gallery" title=""><i class="fas fa-plus"></i></a>
								</div>
								<h3 class="item-title"><a href="{{ route('portfolio.show', $other->slug) }}">{{ $other->title }}</a></h3>
								<p>{{ $other->category }}</p>
							</div>
						</div>
					@endforeach
				</div>
			</div>
			@endif
		</div>
	</section>
@endsection
