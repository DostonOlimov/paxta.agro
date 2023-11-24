<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>
<!-- Humberger Begin -->
<div class="humberger__menu__overlay"></div>
<div class="humberger__menu__wrapper">
    <div class="humberger__menu__logo">
        <a href="#"><img src="/assets/images/logoNEW.png" alt=""></a>
    </div>
    <div class="humberger__menu__cart">
    </div>
    <div class="humberger__menu__widget">
        <div class="header__top__right__auth">
            @if (auth()->user())
                <a class="dropdown-item" href="#" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <svg class="icon me-2">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                    </svg> Tizimdan chiqish  <form id="logout-form" action="{{route('logout')}}" method="POST"
                                                   style="display: none;">
                        @csrf
                    </form></a>
            @else
                <a href="{{ url('/login') }}"> <i class="fa fa-user"></i>Kirish</a>
            @endif
        </div>
    </div>
    <nav class="humberger__menu__nav mobile-menu">
        <ul>
            <li class="active"><a href="{{ url('/') }}">Asosiy</a></li>
            <li><a href="{{ url('/application/my-applications') }}">Mening arizalarim</a></li>
            <li><a href="{{ url('/organization/my-organization-add') }}">Ariza berish</a></li>
            <li><a href="{{ url('/all') }}">Barchasi</a></li>
        </ul>
    </nav>
    <div id="mobile-menu-wrap"></div>
    <div class="header__top__right__social">
        <a href="#"><i class="fa fa-facebook"></i></a>
        <a href="#"><i class="fa fa-twitter"></i></a>
        <a href="#"><i class="fa fa-linkedin"></i></a>
        <a href="#"><i class="fa fa-pinterest-p"></i></a>
    </div>
    <div class="humberger__menu__contact">
        <ul>
            <h5>Ishonch telefoni</h5>
            <span>(71) 202-12-48</span>
        </ul>
    </div>
</div>
<!-- Humberger End -->

<!-- Header Section Begin -->
<header class="header" >
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-md-9">
                    <div class="header__top__left row">
                        <div class="col-md-2 div1"><i class="fa fa-envelope">info@agroin.uz</i></div>
                        <div class="col-md-9 div2"><b>Qishloq xo‘jalik ekinlari urug‘larini sertifikatlashtirish tizimi</b></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="header__top__right">
                        <div class="header__top__right__social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                            <a href="#"><i class="fa fa-pinterest-p"></i></a>
                        </div>
                        <div class="header__top__right__auth">
                            @if (auth()->user())
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"><i class="fa fa-sign-out"></i>Chiqish</button>
                                </form>
                            @else
                                <a href="{{ url('/login') }}"> <i class="fa fa-user"></i>Kirish</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <a  href="{{ url('/') }}"> <img style="width:100px;" src="/img/logoNEW.png"></a>
                </div>
            </div>
            <div class="col-lg-8">
                <nav class="header__menu">
                    <ul>
                        <li class="active"><a href="{{ url('/') }}">Asosiy</a></li>
                        <li><a href="{{ url('/application/my-applications') }}">Mening arizalarim</a></li>
                        <li><a href="{{ url('/organization/my-organization-add') }}">Ariza berish</a></li>
                        <li><a href="{{ url('/all') }}">Boshqalar</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-2">
                <div class="hero__search__phone">
                    <div class="hero__search__phone__icon">
                        <i class="fa fa-phone"></i>
                    </div>
                    <div class="hero__search__phone__text">
                        <h5>Ishonch telefoni</h5>
                        <span>(71) 202-12-48</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="humberger__open">
            <i class="fa fa-bars"></i>
        </div>
    </div>
</header>
<!-- Header Section End -->
