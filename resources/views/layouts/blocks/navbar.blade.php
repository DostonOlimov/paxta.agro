<header class="header header-sticky">
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button><a class="header-brand d-md-none" href="#">
            <svg width="90" height="46" alt="CoreUI Logo">
                <use xlink:href="/assets/brand/coreui.svg#full"></use>
            </svg></a>
    @php
        $currentFlag = "/img/flags/uzbekistan.png";
        if(app()->getLocale() == 'ru'){
            $currentFlag = "/img/flags/russia.png";
        }elseif (app()->getLocale() == 'en'){
            $currentFlag = "/img/flags/united-kingdom.png";
        }
    @endphp
        <div class="d-flex align-items-center justify-content-around" style="width: 86%;">
            <div>
                <div class="dropdown">
                    <img id="currentFlag" src="{{$currentFlag}}" class="flag">
                    <div class="my-dropdown-content">
                        <a href="#" onclick="changeLanguage('uz')"><img src="/img/flags/uzbekistan.png" class="flag">O'zbek</a>
                        <a href="#" onclick="changeLanguage('krill')"><img src="/img/flags/uzbekistan.png" class="flag">Ўзбек</a>
                        <a href="#" onclick="changeLanguage('en')"><img src="/img/flags/united-kingdom.png" class="flag">English</a>
                        <a href="#" onclick="changeLanguage('ru')"><img src="/img/flags/russia.png"  class="flag">Русский</a>
                    </div>
                </div>
            </div>
            <div class="title" style="width: 100%; text-align: center;">
                <h1 style="text-transform: uppercase;">{{ trans('message.Paxta tolasini sertifikatlashtirish avtomatlashtirilgan axborot tizimi') }}</h1>
            </div>

        </div>
        <ul class=" ms-3">
            <li class="nav-item dropdown"><a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-md"><img class="avatar-img" src="/assets/images/8.jpg" alt="user@email.com"></div>
                </a>
                <div class="dropdown-menu dropdown-menu-start pt-0" style="transform: translateX(-180px) translateY(30px)">
                    <div class="dropdown-header bg-light">
                            <div class="fw-semibold">{{auth()->user()->name.' '.auth()->user()->lastname}}</div>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                        </svg> {{trans('message.Shaxsiy ma\'lumotlar')}}</a><a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg> {{trans('message.Settings')}}</a>
                        <a class="dropdown-item" href="#" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <svg class="icon me-2">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                        </svg> {{trans('message.Tizimdan chiqish')}}  <form id="logout-form" action="{{route('logout')}}" method="POST"
                                          style="display: none;">
                                        @csrf
                            </form></a>
                    </div>
                </div>
            </li>
        </ul>
    </div>

</header>
