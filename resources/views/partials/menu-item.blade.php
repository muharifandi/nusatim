@php
    $hasChildren = $item->children->isNotEmpty();
@endphp

@if($depth === 1 && $item->type === 'mega_menu' && $hasChildren)
    <li class="position-static d-none d-lg-block">
        <a href="{{ $item->url ?: '#' }}">{{ $item->label }}</a>
        <div class="mega-menu-container">
            <div class="row">
                @foreach($item->children as $child)
                    <div class="col-lg-4">
                        <div class="mega-menu-box">
                            <div class="mega-menu-figure">
                                <a href="{{ $child->url ?: '#' }}">
                                    <img src="{{ $child->image ? asset($child->image) : asset('media/menu/home1.jpg') }}" alt="{{ $child->label }}">
                                </a>
                            </div>
                            <div class="mega-menu-title">
                                <h3 class="item-title"><a href="{{ $child->url ?: '#' }}">{{ $child->label }}</a></h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </li>
    <li class="d-block d-lg-none">
        <a href="#">{{ $item->label }}</a>
        <ul class="dropdown-menu-col-1">
            @foreach($item->children as $child)
                <li><a href="{{ $child->url ?: '#' }}"><span>{{ $child->label }}</span></a></li>
            @endforeach
        </ul>
    </li>
@elseif($hasChildren)
    @php
        $childClass = $depth === 1 ? 'dropdown-menu-col-1' : ($depth === 2 ? 'second-level' : 'third-level');
        $liClass = $depth === 2 ? 'has-child-second-level' : ($depth === 3 ? 'has-child-third-level' : null);
    @endphp
    <li{!! $liClass ? ' class="'.$liClass.'"' : '' !!}>
        <a href="#">@if($depth === 1){{ $item->label }}@else<span>{{ $item->label }}</span>@endif</a>
        <ul class="{{ $childClass }}">
            @foreach($item->children as $child)
                @include('partials.menu-item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    </li>
@else
    <li><a href="{{ $item->url ?: '#' }}"><span>{{ $item->label }}</span></a></li>
@endif
