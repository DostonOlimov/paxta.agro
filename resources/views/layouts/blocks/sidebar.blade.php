<div class="sidebar sidebar-dark sidebar-fixed " id="sidebar" >
    <div class="sidebar-brand d-none d-md-flex justify-content-around">
        <img style="width:70px;" src="/resources/assets/images/paxta_logo.png">
        <h2 style="font-size: 20px; color: white; margin: 5px; !important;">AGROINSPEKSIYA</h2>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link" href="/home">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-home"></use>
                </svg> Bosh sahifa</a></li>
        @if(auth()->user()->role != \App\Models\User::STATE_EMPLOYEE)
        <li class="nav-item"><a class="nav-link" href="{!! url('full-report') !!}">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                </svg> Umumiy ro'yxat</a></li>
        @endif
        <li class="nav-title">Bo'limlar</li>


        <li class="nav-item"><a class="nav-link" href="{!! url('/application/list') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-cursor"></use>
                </svg> Arizalar</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/decision/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-folder-open"></use>
                </svg>Qaror va Sinov dasturlari</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/dalolatnoma/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-inbox"></use>
                </svg>Na'muna olish dalolatnomalari</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/akt_amount/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-balance-scale"></use>
                </svg>Og'irlik bo'yicha dalolatnomalar</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/akt_laboratory/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-search"></use>
                </svg>Laboratoriya ma'lumotlari</a></li>
        @if(auth()->user()->role != \App\Models\User::ROLE_DIROCTOR)
        <li class="nav-title">Tizim sozlamalari</li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-plant"></use>
                </svg>Paxta</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{{ url('/crops_name/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg> Nomlar ro'yxati</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/crops_type/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg>Navlar ro'yxati</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/crops_generation/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg>Sinflar ro'yxatii</a>
                </li>
            </ul>
        </li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-factory"></use>
                </svg> Korxona va tashkilotlar</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{{ url('/organization/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cis-map"></use>
                        </svg> Buyurtmachilar korxonalar</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/prepared/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-city"></use>
                        </svg>Ishlab chiqaruvchi zavodlar</a>
                </li>
            </ul>
        </li>
            <li class="nav-item"><a class="nav-link" href="{!! url('/laboratories/list') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-beaker"></use>
                    </svg> Laboratoriyalar</a></li>
        @if(auth()->user()->role != \App\Models\User::STATE_EMPLOYEE)
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-command"></use>
                </svg> Normativ hujjatlar</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{!! url('/nds/list') !!}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use></use>
                        </svg> Normativ hujjatlar</a></li>
                <li class="nav-item"><a class="nav-link"  href="{!! url('/indicator/list') !!}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use></use>
                        </svg>Sifat ko'rsatkichlari</a></li>
            </ul>
        </li>
        <li class="nav-title">Sozlamalar</li>

        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                </svg> Sozlamalar</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link" href="{{ url('/production/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg>Ishlab chiqarish turi</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/requirement/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg>Talab etiluvchi hujjatlar</a>
                </li>
            </ul>
        </li>
        @endif
        @endif
    @if ( auth()->user()->role == 'admin')


        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                </svg> Foydalanuvchilar</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{!! url('/employee/list') !!}"><span class="nav-icon"></span>
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-group"></use>
                </svg> Ro'yxat</a></li>
                <li class="nav-item"><a class="nav-link" href="{!! url('/employee/add')!!}"><span class="nav-icon"></span>
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user-plus"></use>
                </svg> Yangi qo'shish</a>
            </li>
            </ul>
        </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-map"></use>
                    </svg> Hududlar</a>
                <ul class="nav-group-items">
                    <li class="nav-item"><a class="nav-link"  href="{{ url('/states/list') }}"><span class="nav-icon"></span>
                            <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cis-map"></use>
                            </svg> Viloyatlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/cities/list') }}"><span class="nav-icon"></span>
                            <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-city"></use>
                            </svg>Shaxar va tumanlar</a>
                        </li>
                </ul>
            </li>
        @endif
        <li class="nav-item"><a class="nav-link" href="#"></a></li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>


