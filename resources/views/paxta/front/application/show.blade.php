@extends('layouts.front')

@section('content')
    <style>
        .right_side .table_row, .member_right .table_row {
            border-bottom: 1px solid #dedede;
            float: left;
            width: 100%;
            padding: 1px 0px 4px 2px;
        }

        .table_row .table_td {
            padding: 8px 8px !important;
        }
        .txt_color a:visited{
            color:blue !important;
        }
    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-file mr-1"></i>&nbsp Ariza ma'lumotlari
                </li>
            </ol>
        </div>
        @if(session('message'))
            <div class="row massage">
                <div class="col-md-12 col-sm-12">
                    <div class="alert alert-success text-center">
                        <input id="checkbox-10" type="checkbox" checked="">
                        <label for="checkbox-10 colo_success">  {{session('message')}} </label>
                    </div>
                </div>
            </div>
        @endif
        @if($user->comment)
            <div class="row massage">
                <div class="col-md-12 col-sm-12">
                    <div class="alert alert-danger text-center">
                        <label for="checkbox-10 colo_danger">{{optional($user->comment)->comment}}</label>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li class="btn-danger">
                                        <a class="text-light" href="{{  url('/application/my-applications') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-arrow-left">&nbsp;</i> Orqaga
                                        </a>
                                    </li>
                                    <li class="btn-success">
                                        <a class="text-light" href="{!! url('/application/my-application-edit/'.$user->id)!!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-edit fa-lg">&nbsp;</i> {{ trans('app.Edit')}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 right_side">
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Ariza raqami</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                            {{ $user->app_number == 0 ? '-' : $user->app_number }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Ariza sanasi</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $user->date }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Urugʼlik tayorlangan shaxobcha yoki sexning nomi</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->prepared)->name  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Ekin turi</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops->name)->name  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Ekin navi</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops->type)->name  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Ekin avlodi</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops->generation)->name  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Kod TN VED</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->kodtnved  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Partiya raqami</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->party_number  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Miqdori</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->amount_name  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Hosil yili</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->year  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Qo'shimcha ma'lumot</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $user->data  }}
                                            </span>
                                            </div>
                                        </div>
                                        <div class="table_row row">
                                            <div class="col-md-5 col-sm-12 table_td">
                                                <b>Ishlab chiqarish turi</b>
                                            </div>
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @foreach($production_type as $type)
                                                {{ optional($type->type)->name  }},
                                                @endforeach
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="panel panel-primary">
                                            <div class="tab_wrapper page-tab">
                                                <ul class="tab_list">
                                                    <li class="btn-success">
                                                        <a class="text-light" href="{!! url('/organization/my-organization-edit/'.$company->id) !!}">
                                                            <span class="visible-xs"></span>
                                                            <i class="fa fa-edit">&nbsp;</i>
                                                            <b >O'zgartirish</b>
                                                        </a>
                                                    </li>
                                                    <h5>  Buyurtmachi korxona yoki tashkilot ma'lumotlari</h5>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="row">
                                                    <div class="col-md-8 col-sm-12 right_side">
                                                        <div class="table_row row">
                                                            <div class="col-md-5 col-sm-12 table_td">
                                                                <b>Tashkilot  STIRi</b>
                                                            </div>
                                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                            {{ $company->inn }}
                                            </span>
                                                            </div>
                                                        </div>
                                                        <div class="table_row row">
                                                            <div class="col-md-5 col-sm-12 table_td">
                                                                <b>Tashkilot nomi</b>
                                                            </div>
                                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $company->name }}
                                            </span>
                                                            </div>
                                                        </div>

                                                        <div class="table_row row">
                                                            <div class="col-md-5 col-sm-12 table_td">
                                                                <b>Tashkilot manzili</b>
                                                            </div>
                                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($company->city)->region->name .' '.optional($company->city)->name . ' ' .$company->address  }}
                                            </span>
                                                            </div>
                                                        </div>
                                                        <div class="table_row row">
                                                            <div class="col-md-5 col-sm-12 table_td">
                                                                <b>Tashkilot rahbari</b>
                                                            </div>
                                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $company->owner_name  }}
                                            </span>
                                                            </div>
                                                        </div>
                                                        <div class="table_row row">
                                                            <div class="col-md-5 col-sm-12 table_td">
                                                                <b>Tashkilot telefon raqami</b>
                                                            </div>
                                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $company->phone_number  }}
                                            </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                            @if($user->type == 1)
                                                <div class="panel panel-primary">
                                                    <div class="tab_wrapper page-tab">
                                                        <ul class="tab_list">
                                                            <li class="btn-success">
                                                                <a class="text-light" href="{!! url('/application/my-file-local-edit/'.$user->id) !!}">
                                                                    <span class="visible-xs"></span>
                                                                    <i class="fa fa-edit">&nbsp;</i>
                                                                    <b >O'zgartirish</b>
                                                                </a>
                                                            </li>
                                                            <h5>Talab etilgan hujjatlar</h5>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Aprobatsiya dalolatnomasi</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->local_file)->a_dalolatnoma)
                                                <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->local_file)->a_dalolatnoma_file) }}" ><i class="fa fa-download"></i> Dalolatnoma fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Aprobatsiya xulosasi</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->local_file)->a_xulosa)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->local_file)->a_xulosa_file) }}" ><i class="fa fa-download"></i> Xulosa fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Dorilash xulosasi </b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                 @if(optional($user->local_file)->d_xulosa)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->local_file)->d_xulosa_file) }}" ><i class="fa fa-download"></i> Xulosa fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Markirovka</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                               @if(optional($user->local_file)->markirovka)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->local_file)->markirovka_file) }}" ><i class="fa fa-download"></i> Markirovka fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif($user->type == 2)
                                                <div class="panel panel-primary">
                                                    <div class="tab_wrapper page-tab">
                                                        <ul class="tab_list">
                                                            <li class="btn-success">
                                                                <a class="text-light" href="{!! url('/application/my-file-foreign-edit/'.$user->id) !!}">
                                                                    <span class="visible-xs"></span>
                                                                    <i class="fa fa-edit">&nbsp;</i>
                                                                    <b >O'zgartirish</b>
                                                                </a>
                                                            </li>
                                                            <h5>Talab etilgan hujjatlar</h5>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Karantin ruxsatnomasi(IKR)</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->foreign_file)->karantin)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->karantin_file) }}" ><i class="fa fa-download"></i> Ruxsatnoma fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Fitosanitar xulosasi</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->foreign_file)->fitosanitar)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->fitosanitar_file) }}" ><i class="fa fa-download"></i> Xulosa fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Muvofiqlik sertifikati </b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                 @if(optional($user->foreign_file)->sertifikat)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->sertificat_file) }}" ><i class="fa fa-download"></i> Sertifikat fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Markirovka</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                               @if(optional($user->foreign_file)->markirovka)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->markirovka_file) }}" ><i class="fa fa-download"></i> Markirovka fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="table_row row">
                                        <div class="col-md-5 col-sm-12 table_td">
                                            <b>Invoys</b>
                                        </div>
                                        <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->foreign_file)->invoys)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->invoys_file) }}" ><i class="fa fa-download"></i> Invoys fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="table_row row">
                                        <div class="col-md-5 col-sm-12 table_td">
                                            <b>Yuk xati</b>
                                        </div>
                                        <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->foreign_file)->yuk_xati)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->yuk_xati_file) }}" ><i class="fa fa-download"></i> Yuk xati fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="table_row row">
                                        <div class="col-md-5 col-sm-12 table_td">
                                            <b>SMR</b>
                                        </div>
                                        <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->foreign_file)->smr)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->foreign_file)->smr_file) }}" ><i class="fa fa-download"></i> SMR fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                        @else
                                                <div class="panel panel-primary">
                                                    <div class="tab_wrapper page-tab">
                                                        <ul class="tab_list">
                                                            <li class="btn-success">
                                                                <a class="text-light" href="{!! url('/application/my-file-old-edit/'.$user->id) !!}">
                                                                    <span class="visible-xs"></span>
                                                                    <i class="fa fa-edit">&nbsp;</i>
                                                                    <b >O'zgartirish</b>
                                                                </a>
                                                            </li>
                                                            <h5>Talab etilgan hujjatlar</h5>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="table_row row">
                                                        <div class="col-md-5 col-sm-12 table_td">
                                                            <b>Avvalda rasmiylashtirilgan Muvofiqlik sertifikati</b>
                                                        </div>
                                                        <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                @if(optional($user->local_file)->certificate)
                                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url(optional($user->local_file)->old_certificate_file) }}" ><i class="fa fa-download"></i> Sertifikat fayli</a>
                                                @else
                                                    Fayl yuklanmagan
                                                @endif
                                            </span>
                                                        </div>
                                                    </div>
                                        @endif
                                    </div>

                                    @can('edit', $user)
                                        <div class="col-12 text-right m-2">
                                            <a href="/application/edit/{{ $user->id }}">
                                                <button class="btn btn-primary">O'zgartirish</button>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection
