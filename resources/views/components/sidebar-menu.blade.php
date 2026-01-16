@props(['menuService'])

@php
    use Illuminate\Support\Str;
    
    $menuItems = $menuService->getVisibleMenuItems();
@endphp

<ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
    @foreach($menuItems as $item)
        @if($item['type'] === 'title')
            <li class="nav-title">{{ trans($item['label']) }}</li>
        
        @elseif($item['type'] === 'item')
            <li class="nav-item">
                <a class="nav-link {{ isset($item['activePattern']) && Request::is($item['activePattern']) ? 'active1' : (Request::is($item['route']) ? 'active1' : '') }}"
                   href="{{ isset($item['route']) && Str::startsWith($item['route'], '/') ? url($item['route']) : (isset($item['route']) ? route($item['route']) : '#') }}">
                    @if(isset($item['icon']))
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#{{ $item['icon'] }}"></use>
                        </svg>
                    @endif
                    {!! isset($item['label']) ? nl2br(trans($item['label'])) : '' !!}
                </a>
            </li>
        
        @elseif($item['type'] === 'group')
            <li class="nav-group">
                <a class="nav-link nav-group-toggle">
                    @if(isset($item['icon']))
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#{{ $item['icon'] }}"></use>
                        </svg>
                    @endif
                    {{ trans($item['label']) }}
                </a>
                <ul class="nav-group-items">
                    @foreach($item['children'] as $child)
                        <li class="nav-item {{ isset($child['activePattern']) && Request::is($child['activePattern']) ? 'active1' : '' }}">
                            <a class="nav-link {{ isset($child['activePattern']) && Request::is($child['activePattern']) ? 'active1' : (Request::is($child['route']) ? 'active1' : '') }}"
                               href="{{ isset($child['route']) && Str::startsWith($child['route'], '/') ? url($child['route']) : (isset($child['route']) ? route($child['route']) : '#') }}">
                                @if(isset($child['icon']))
                                    <svg class="nav-icon">
                                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#{{ $child['icon'] }}"></use>
                                    </svg>
                                @endif
                                {!! isset($child['label']) ? nl2br(trans($child['label'])) : '' !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endif
    @endforeach
    
    <li class="nav-item"><a class="nav-link" href="#"></a></li>
</ul>