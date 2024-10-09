<style>
    .my_header .nav-bg {
        position: absolute;
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
                <a href="{{ url('/') }}" class="nav-logo js-anchor-link"><img src="{{ asset('img/logoNEW.png') }}"
                        alt="Logo" /></a>

                <h4 style="color: white;">PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI</h4>

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
                        <svg width="20px" height="20px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h16v16H0z" />
                            <path d="M14 14V2H8V0h8v16H8v-2zm-9.002-.998L0 8l5-5 1.416 1.416L3.828 7H12v2H3.828l2.586 2.586z" />
                        </svg>
                        <div class="tizimdan-chiqish-text">{{trans('message.Tizimdan chiqish')}}</div>
                    </div>
                </div>

            </nav>
        </div>
    </div>
</header>