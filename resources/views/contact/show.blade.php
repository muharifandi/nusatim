@extends('layouts.app')

@push('styles')
	<style>
		.map-embed-frame {
			margin-top: 60px;
			height: 450px;
			border-radius: 10px;
		}

		@media only screen and (max-width: 767px) {
			.map-embed-frame {
				height: 350px;
			}
		}

		@media only screen and (max-width: 575px) {
			.map-embed-frame {
				height: 300px;
			}
		}
	</style>
@endpush

@section('content')
	@include('partials.page-banner', ['title' => $page?->field('banner_title', 'Contact Us')])

	<section class="contact-wrap-layout3 section-padding-md-equal">
		<div class="container">
			<div class="row gutters-50">
				<div class="col-lg-4 mb-5 has-animation">
					<div class="single-item translate-left-75 opacity-animation transition-150 transition-delay-100">
						<div class="address-box-layout2">
							<div class="item-icon"><i class="flaticon-mail"></i></div>
							<div class="item-content">
								<h3 class="item-title">Email &amp; Phone</h3>
								<ul class="list-item">
									<li>{{ $siteSettings->email }}</li>
									<li>{{ $siteSettings->phone }}</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="single-item translate-left-75 opacity-animation transition-150 transition-delay-200">
						<div class="address-box-layout2">
							<div class="item-icon"><i class="flaticon-placeholder"></i></div>
							<div class="item-content">
								<h3 class="item-title">Our Location</h3>
								<ul class="list-item">
									<li>{{ $siteSettings->address }}</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="single-item translate-left-75 opacity-animation transition-150 transition-delay-300">
						<div class="address-box-layout2">
							<div class="item-icon"><i class="flaticon-share"></i></div>
							<div class="item-content">
								<h3 class="item-title">Get In Touch</h3>
								<ul class="list-item">
									<li>{{ $siteSettings->email }}</li>
									<li>{{ $siteSettings->phone }}</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-8 has-animation">
					<div class="contact-box-layout3">
						@if(session('status'))
							<div class="alert alert-success">{{ session('status') }}</div>
						@endif
						<form class="contact-form-box" id="contact-form" method="POST" action="{{ route('contact.store') }}">
							@csrf
							<div class="row">
								<div class="col-md-6 form-group">
									<div class="translate-bottom-50 opacity-animation transition-100 transition-delay-300">
										<input type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') }}" data-error="Name field is required" required>
										<div class="help-block with-errors">@error('name'){{ $message }}@enderror</div>
									</div>
								</div>
								<div class="col-md-6 form-group">
									<div class="translate-bottom-50 opacity-animation transition-100 transition-delay-400">
										<input type="email" placeholder="Email" class="form-control" name="email" value="{{ old('email') }}" data-error="Email field is required" required>
										<div class="help-block with-errors">@error('email'){{ $message }}@enderror</div>
									</div>
								</div>
								<div class="col-12 form-group">
									<div class="translate-bottom-50 opacity-animation transition-100 transition-delay-400">
										<input type="text" placeholder="Subject" class="form-control" name="subject" value="{{ old('subject') }}" data-error="Subject field is required" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="col-12 form-group">
									<div class="translate-bottom-50 opacity-animation transition-100 transition-delay-500">
										<textarea placeholder="Comment" class="textarea form-control" name="message" id="form-message" rows="7" cols="20" data-error="Message field is required" required>{{ old('message') }}</textarea>
										<div class="help-block with-errors">@error('message'){{ $message }}@enderror</div>
									</div>
								</div>
								<div class="col-12 form-group mb-0">
									<div class="translate-bottom-50 opacity-animation transition-100 transition-delay-500">
										<button type="submit" class="btn-fill btn-gradient">Send Message</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="box-bottom-margin">
				<div class="google-map">
					<iframe
						class="map-embed-frame"
						style="width: 100%; border: 0;"
						src="{{ $siteSettings->google_maps_embed_url ?: 'https://www.google.com/maps?q='.urlencode($siteSettings->address ?: 'Jakarta, Indonesia').'&output=embed' }}"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
					></iframe>
				</div>
			</div>
		</div>
	</section>

	@include('partials.brand-carousel')
@endsection
