<style>
    .my_header .nav-bg {
        position: absolute;
    }

    .my_header .nav-logo {
        margin-right: 0;
    }

    .my-dropdown-content {
        right: 0px !important;
    }

    .header {
        padding-top: 0;
        padding-bottom: 0;
        background: #fff;
        border-bottom: none;
    }

    .right-side-header {
        display: flex;
        align-items: center;
        column-gap: 25px;
    }

    .tizimdan-chiqish svg {
        cursor: pointer;
        margin-top: 10px;
        fill: #fff;
    }

    .tizimdan-chiqish {
        cursor: pointer;
        position: relative;
        display: inline-block;
    }

    .tizimdan-chiqish-text {
        user-select: none;
        visibility: hidden;
        opacity: 0;
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 7px 15px;
        border-radius: 4px;
        position: absolute;
        top: 45px;
        left: 50%;
        transform: translateX(-50%);
        transition: opacity 0.3s, visibility 0.3s;
        white-space: nowrap;
        z-index: 1;
    }

    .tizimdan-chiqish:hover .tizimdan-chiqish-text {
        visibility: visible;
        opacity: 1;
    }

    h4 {
        margin-top: 5px;
        margin-bottom: 0 !important;
        padding: 0px !important;
    }

    .logo-left-container {
        display: flex;
        align-items: center;
    }

    .dropdown-pcScreen {
        display: none;
    }

    @media (max-width: 768px) {
        .my-dropdown-year {
            right: 9px !important;
        }

        .menu-year {
            width: 64px !important;
            margin: 3px 0;
        }

        .dropdown-mobileScreen {
            display: none;
        }

        .dropdown-pcScreen {
            display: block;
        }

        .right-side-header {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            grid-column-gap: 13px;
            grid-row-gap: 0px;
        }

        .dropdown {
            grid-area: 1 / 1 / 2 / 2;
        }

        .tizimdan-chiqish {
            grid-area: 1 / 2 / 2 / 3;
        }

        .dropdown-pcScreen {
            grid-area: 2 / 1 / 3 / 3;
        }

        h4 {
            max-width: 177px !important;
            font-size: 13px;
        }
    }
</style>
@php
$currentFlag = "/img/flags/uzbekistan.png";
if(app()->getLocale() == 'ru'){
$currentFlag = "/img/flags/russia.png";
}elseif (app()->getLocale() == 'en'){
$currentFlag = "/img/flags/united-kingdom.png";
}
@endphp
<header class="header my_header" id="navigation-menu">
    <div class="nav-bg">
        <div class="container">
            <nav class="navbar">
                <div class="logo-left-container">
                    <a href="{{ url('/sifat-sertificates/list') }}" class="nav-logo js-anchor-link"><img src="{{ asset('/resources/assets/images/paxta_logo.png') }}"
                            alt="Logo" /></a>
                    <div class="dropdown dropdown-mobileScreen">
                        <div id="currentYear" class="menu-year" style="color: var(--main-font-color); background-color: white; cursor: pointer; padding-bottom: 33px !important;"><i class="fa fa-list"></i> </div>
                        <div class="my-dropdown-year">
                            <a href="{!! url('/sifat-sertificates/list') !!}"><b>Sifat sertifikatlari</b></a>
                            <a href="{!! url('/sifat-contracts/list') !!}"><b>Shartnomalar</b></a>
                        </div>
                    </div>
                </div>
                <h4 style="color: white;">PAXTA MAHSULOTLARINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN TIZIMI</h4>

                <div class="right-side-header">
                    <div class="dropdown">
                        <img id="currentFlag" src="{{$currentFlag}}" class="flag">
                        <div class="my-dropdown-content">
                            <a href="#" onclick="changeLanguage('uz')"><img src="/img/flags/uzbekistan.png" class="flag">O'zbek</a>
                            <a href="#" onclick="changeLanguage('krill')"><img src="/img/flags/uzbekistan.png" class="flag">Ўзбек</a>
                            <a href="#" onclick="changeLanguage('en')"><img src="/img/flags/united-kingdom.png" class="flag">English</a>
                            <a href="#" onclick="changeLanguage('ru')"><img src="/img/flags/russia.png" class="flag">Русский</a>
                        </div>
                    </div>
                    <div class="tizimdan-chiqish">
                        <a href="#" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <svg width="20px" height="20px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h16v16H0z" />
                                <path d="M14 14V2H8V0h8v16H8v-2zm-9.002-.998L0 8l5-5 1.416 1.416L3.828 7H12v2H3.828l2.586 2.586z" />
                            </svg>
                            <form id="logout-form" action="{{route('logout')}}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </a>
                        <div class="tizimdan-chiqish-text">{{trans('message.Tizimdan chiqish')}}</div>
                    </div>

                    <div class="dropdown dropdown-pcScreen">
                        <div id="currentYear" class="menu-year" style="color: var(--main-font-color); background-color: white; cursor: pointer; padding-bottom: 33px !important;"><i class="fa fa-list"></i> </div>
                        <div class="my-dropdown-year">
                            <a href="{!! url('/sifat-sertificates/list') !!}"><b>Sifat sertifikatlari</b></a>
                            <a href="{!! url('/sifat-contracts/list') !!}"><b>Shartnomalar</b></a>
                        </div>
                    </div>
                </div>

            </nav>
        </div>
    </div>
</header>