@if($clients->isNotEmpty())
<section id="brand-wrap-layout1" class="brand-wrap-layout1 {{ $bgClass ?? 'bg-color-accent2' }}">
	<div class="container">
		<div class="rc-carousel nav-control-simple nav-center" data-loop="true" data-items="8" data-margin="30" data-autoplay="{{ ($autoplay ?? false) ? 'true' : 'false' }}" data-autoplay-timeout="5000" data-smart-speed="700" data-dots="false" data-nav="true" data-nav-speed="false" data-r-x-small="{{ $rXSmall ?? 2 }}"
		 data-r-x-small-nav="true" data-r-x-small-dots="false" data-r-x-medium="{{ $rXMedium ?? 3 }}" data-r-x-medium-nav="true" data-r-x-medium-dots="false" data-r-small="{{ $rSmall ?? 4 }}" data-r-small-nav="true" data-r-small-dots="false" data-r-medium="{{ $rMedium ?? 4 }}" data-r-medium-nav="true" data-r-medium-dots="false" data-r-large="5"
		 data-r-large-nav="true" data-r-large-dots="false" data-r-extra-large="6" data-r-extra-large-nav="true" data-r-extra-large-dots="false">
			@foreach($clients as $client)
				<div class="brand-box-layout1"><img src="{{ asset($client->logo) }}" alt="{{ $client->name }}"></div>
			@endforeach
		</div>
	</div>
</section>
@endif
