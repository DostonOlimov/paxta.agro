<div class="panel panel-primary">
    <div class="tab_wrapper page-tab">
        <ul class="tab_list">
            @foreach ($tabs as $tab)
                <li class="nav-item {{ request()->is($tab['url']) ? 'active' : '' }}">
                    <a href="{{ url($tab['url']) }}" class="nav-link" style="color:blue;">
                        <i class="visible-xs fa {{ $tab['icon'] }} fa-lg"></i><b>{{ trans($tab['name']) }}</b>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
