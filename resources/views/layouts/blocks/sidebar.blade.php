<div class="sidebar sidebar-dark sidebar-fixed " id="sidebar" >
    <div class="sidebar-brand d-none d-md-flex justify-content-around">
        <img style="width:55px; margin-top: 9px;" src="/resources/assets/images/paxta_logo.png">
                <h2 style="font-size: 20px; color: white; margin: 6px 6px 5px -11px; !important;">{{ trans('message.AGROINSPEKSIYA') }}</h2>

    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link" href="/home">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-home"></use>
                </svg>{{ trans('message.Bosh sahifa') }}</a></li>
{{--
        <li class="nav-item"><a class="nav-link" href="{!! url('full-report') !!}">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                </svg>{{trans('message.Umumiy ro\'yxat')}}</a></li> --}}
{{-- start --}}
            <li class="nav-group">
                <a class="nav-link nav-group-toggle">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-folder"></use>
                    </svg>{{trans('message.Hisobotlar')}}
                </a>
                <ul class="nav-group-items">
                    <li class="nav-item"><a class="nav-link" href="{!! url('full-report') !!}">
                            <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                            </svg>{{trans('message.Umumiy ro\'yxat')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{!! url('/akt_laboratory/search') !!}"> <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-search"></use>
                        </svg>{{trans('message.Laboratoriya ma\'lumotlari')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{!! url('organization-company-report') !!}">
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-building"></use>
                        </svg>{{trans("message.Korxonalar kesimda ma'lumot")}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{!! url('prepared-company-report') !!}">
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-institution"></use>
                        </svg>{{trans("message.Ishlab chiqaruvchi zavodlar kesimda ma'lumot")}}</a></li>
                </ul>
            </li>
{{-- end --}}
        @if(auth()->user()->crop_branch != \App\Models\User::CROP_BRANCH_CHIGIT)
        <li class="nav-title">{{trans('message.Sertifikatsiya')}}</li>


        <li class="nav-item "><a class="nav-link {{ \Illuminate\Support\Facades\Request::is('application/*') ? 'active' : ''}}" href="{!! url('/application/list') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-cursor"></use>
                </svg>{{trans('message.Arizalar')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/decision/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-folder-open"></use>
                </svg><?php echo nl2br(trans('message.Qaror va Sinov dasturlari')); ?></a></li>

        <li class="nav-item"><a class="nav-link" href="{!! url('/dalolatnoma/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-inbox"></use>
                </svg><?php echo nl2br(trans('message.Na\'muna olish dalolatnomalari')); ?></a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/akt_amount/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-balance-scale"></use>
                </svg><?php echo nl2br(trans('message.Og\'irlik bo\'yicha dalolatnomalar')); ?></a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/humidity/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-eyedropper"></use>
                </svg>{{trans('message.Namlik dalolatnomasi')}}</a></li>

        <li class="nav-title">{{trans('message.Laboratoriya')}}</li>

        <li class="nav-item"><a class="nav-link" href="{!! url('/hvi/list') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-devices"></use>
                </svg>{{trans('message.HVI ma\'lumotlari')}}</a></li>

        <li class="nav-item"><a class="nav-link" href="{!! url('/humidity_result/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-chart"></use>
                </svg>{{trans('message.Namlik natijalari')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/measurement_mistake/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-clear-all"></use>
                </svg>{{trans('message.O\'lchash xatoligi')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/final_results/search') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-bar-chart"></use>
                </svg>{{trans('message.Yakuniy natijalar')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="{!! url('/laboratory-protocol/list') !!}"> <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                </svg>{{trans('message.Sinov bayonnomalari')}}</a></li>
        @endif

        @if(auth()->user()->crop_branch != \App\Models\User::CROP_BRANCH_TOLA)
            <li class="nav-title">{{trans('message.Texnik chigit')}}</li>

            <li class="nav-item; {{ \Illuminate\Support\Facades\Request::is('application/*') ? 'active' : ''}}"><a class="nav-link" href="{!! url('/application/list?nameId[eq]=2') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-cursor"></use>
                    </svg>{{trans('message.Arizalar')}}</a></li>
            <li class="nav-item"><a class="nav-link" href="{!! url('/decision/search') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-folder-open"></use>
                    </svg><?php echo nl2br(trans('message.Qaror va Sinov dasturlari')); ?></a></li>

            <li class="nav-item"><a class="nav-link" href="{!! url('/dalolatnoma/search') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-inbox"></use>
                    </svg><?php echo nl2br(trans('message.Na\'muna olish dalolatnomalari')); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="{!! url('/laboratory_results/search') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-bar-chart"></use>
                    </svg>{{trans('message.Laboratoriya natijalari')}}</a></li>
            <li class="nav-item"><a class="nav-link" href="{!! url('/laboratory-protocol/list') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                    </svg>{{trans('message.Sinov bayonnomalari')}}</a></li>
            <li class="nav-item"><a class="nav-link" href="{!! url('/final_results/search') !!}"> <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-bar-chart"></use>
                    </svg>{{trans('message.Yakuniy natijalar')}}</a></li>
        @endif


        <li class="nav-title">{{trans('message.Tizim sozlamalari')}}</li>
        @if(auth()->user()->role != \App\Models\User::ROLE_DIROCTOR)
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-plant"></use>
                </svg>{{trans('message.Paxta')}}</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{{ url('/crops_name/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                        </svg>{{trans('message.Nomlar ro\'yxati')}}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/crops_type/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                        </svg>{{trans('message.Navlar ro\'yxati')}}</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/crops_generation/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                        </svg>{{trans('message.Sinflar ro\'yxatii')}}</a>
                <li class="nav-item"><a class="nav-link" href="{{ url('/crops_selection/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                        </svg>{{trans('message.Seleksiya turlari')}}</a>
                </li>
            </ul>
        </li>
        @endif
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-factory"></use>
                </svg> {{trans('message.Korxona va tashkilotlar')}}</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{{ url('/organization/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-building"></use>
                        </svg> {{trans('message.Buyurtmachilar korxonalar')}}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/prepared/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-institution"></use>
                        </svg>{{trans('message.Ishlab chiqaruvchi zavodlar')}}</a>
                </li>
            </ul>
        </li>
            @if(auth()->user()->role != \App\Models\User::ROLE_DIROCTOR )
            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                    </svg> {{trans('message.Laboratoriya sozlamalari')}}</a>
                <ul class="nav-group-items">
                    <li class="nav-item"><a class="nav-link" href="{!! url('/laboratories/list') !!}"> <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-beaker"></use>
                            </svg> {{trans('message.Laboratoriyalar')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{!! url('/in_xaus/list') !!}"> <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-filter-x"></use>
                            </svg> {{trans('message.In Xaus ma\'lumotlari')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{!! url('/klassiyor/list') !!}"> <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-group"></use>
                            </svg> {{trans('message.Klassiyorlar')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{!! route('laboratory_operators.index') !!}"> <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-list"></use>
                            </svg>{{trans('message.Operatorlar')}}</a></li>
                </ul>
            </li>
        @if(auth()->user()->role != \App\Models\User::STATE_EMPLOYEE)
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-command"></use>
                </svg> {{trans('message.Normativ hujjatlar')}}</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{!! url('/nds/list') !!}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-file"></use>
                        </svg> {{trans('message.Normativ hujjatlar')}}</a></li>
                <li class="nav-item"><a class="nav-link"  href="{!! url('/indicator/list') !!}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                            <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-paperclip"></use>
                        </svg>{{trans('message.Sifat ko\'rsatkichlari')}}</a></li>
            </ul>
        </li>
        <li class="nav-title">{{trans('message.Sozlamalar')}}</li>

        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                </svg> {{trans('message.Sozlamalar')}}</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link" href="{{ url('/production/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg>{{trans('message.Ishlab chiqarish turi')}}</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/requirement/list') }}"><span class="nav-icon"></span>
                        <svg class="nav-icon">
                        </svg>{{trans('message.Talab etiluvchi hujjatlar')}}</a>
                </li>
            </ul>
        </li>
        @endif
        @endif
    @if ( auth()->user()->role == 'admin')


        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                </svg>{{trans('message.Foydalanuvchilar')}}</a>
            <ul class="nav-group-items">
                <li class="nav-item"><a class="nav-link"  href="{!! url('/employee/list') !!}"><span class="nav-icon"></span>
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-group"></use>
                </svg> {{trans('app.Ro\'yxat')}}</a></li>
                <li class="nav-item"><a class="nav-link" href="{!! url('/employee/add')!!}"><span class="nav-icon"></span>
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user-plus"></use>
                </svg> {{trans('app.Qo\'shish')}}</a>
            </li>
            </ul>
        </li>

            <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-map"></use>
                    </svg> {{trans('message.Hududlar')}}</a>
                <ul class="nav-group-items">
                    <li class="nav-item"><a class="nav-link"  href="{{ url('/states/list') }}"><span class="nav-icon"></span>
                            <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cis-map"></use>
                            </svg>{{trans('message.Viloyatlar')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/cities/list') }}"><span class="nav-icon"></span>
                            <svg class="nav-icon">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-city"></use>
                            </svg>{{trans('message.Shaxar va tumanlar')}}</a>
                        </li>
                </ul>
            </li>
        @endif
        <li class="nav-item"><a class="nav-link" href="#"></a></li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>


