@if($activePromotion)
	<!-- In-App Promotion Popup -->
	<div class="promo-popup-overlay" id="promoPopupOverlay" data-active="true" data-promo-id="{{ $activePromotion->id }}">
		<div class="promo-popup-box">
			<button type="button" class="promo-popup-close" id="promoPopupClose" aria-label="Close">&times;</button>
			<a href="{{ $activePromotion->link_url ?: '#' }}">
				<img src="{{ asset($activePromotion->image) }}" alt="{{ $activePromotion->title ?? 'Promo' }}">
			</a>
		</div>
	</div>
@endif
