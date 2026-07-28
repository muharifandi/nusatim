{{-- Decorative animated gradient bubbles, identical across sections except gradient id prefix + per-bubble position/delay. --}}
<ul class="animated-buble">
	@foreach($bubbles as $i => $bubble)
		<li>
			<div class="{{ $bubble['class'] }} opacity-animation transition-200 transition-delay-{{ $bubble['delay'] }}">
				<svg width="{{ $bubble['size'] }}px" height="{{ $bubble['size'] }}px">
					<defs>
						<linearGradient id="{{ $prefix }}{{ $i + 1 }}" x1="0%" x2="50%" y1="86.603%" y2="0%">
							<stop offset="0%" stop-color="rgb(255,211,78)" stop-opacity="1" />
							<stop offset="100%" stop-color="rgb(150,18,226)" stop-opacity="1" />
						</linearGradient>
					</defs>
					@if($bubble['size'] == 627)
						<path fill="url(#{{ $prefix }}{{ $i + 1 }})" opacity="0.1" d="M313.500,0.000 C486.641,0.000 627.000,140.359 627.000,313.500 C627.000,486.641 486.641,627.000 313.500,627.000 C140.359,627.000 0.000,486.641 0.000,313.500 C0.000,140.359 140.359,0.000 313.500,0.000 Z"/>
					@else
						<path fill="url(#{{ $prefix }}{{ $i + 1 }})" opacity="0.1" d="M128.000,0.000 C198.692,0.000 256.000,57.308 256.000,128.000 C256.000,198.692 198.692,256.000 128.000,256.000 C57.307,256.000 -0.000,198.692 -0.000,128.000 C-0.000,57.308 57.307,0.000 128.000,0.000 Z"/>
					@endif
				</svg>
			</div>
		</li>
	@endforeach
</ul>
