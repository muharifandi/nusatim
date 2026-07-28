@if ($paginator->hasPages())
	<nav class="bm-pager" aria-label="Navigasi halaman">
		@if ($paginator->onFirstPage())
			<span class="bm-pager-arrow disabled"><i class="fas fa-chevron-left"></i></span>
		@else
			<a href="{{ $paginator->previousPageUrl() }}" class="bm-pager-arrow"><i class="fas fa-chevron-left"></i></a>
		@endif

		@foreach ($elements as $element)
			@if (is_string($element))
				<span class="bm-pager-dots">{{ $element }}</span>
			@endif

			@if (is_array($element))
				@foreach ($element as $page => $url)
					@if ($page == $paginator->currentPage())
						<span class="bm-pager-link active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="bm-pager-link">{{ $page }}</a>
					@endif
				@endforeach
			@endif
		@endforeach

		@if ($paginator->hasMorePages())
			<a href="{{ $paginator->nextPageUrl() }}" class="bm-pager-arrow"><i class="fas fa-chevron-right"></i></a>
		@else
			<span class="bm-pager-arrow disabled"><i class="fas fa-chevron-right"></i></span>
		@endif
	</nav>
@endif
