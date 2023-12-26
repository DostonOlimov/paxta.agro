@extends('layouts.app')

@section('content')
    @can('accept', $user)
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
                        <i class="fa fa-file mr-1"></i>&nbsp {{trans('app.Ariza ma\'lumotlari')}}
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="panel panel-primary">
                                <div class="tab_wrapper page-tab">
                                    <ul class="tab_list">
                                        <li class="btn-warning">
                                            <a class="text-light" href="{{  url('/application/list') }}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-arrow-left">&nbsp;</i> {{trans('app.Orqaga')}}
                                            </a>
                                        </li>
                                        <li class="btn-primary">
                                            <a class="text-light" href="{!! url('/application/edit/'.$user->id)!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-edit fa-lg">&nbsp;</i> {{ trans('app.Edit')}}
                                            </a>
                                        </li>
                                        @if($user->status == \App\Models\Application::STATUS_NEW)
                                        <li class="btn-success">
                                            <a class="text-light sa-warning" url="{!! url('/application/accept/'.$user->id)!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-check fa-lg">&nbsp;</i> Qabul qilish
                                            </a>
                                        </li>
                                        <li class="btn-danger">
                                            <a class="text-light" href="{!! url('/application/reject/'.$user->id)!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-times fa-lg">&nbsp;</i> Rad etish
                                            </a>
                                        </li>
                                            @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 right_side">
                                            <h4><b>{{trans('app.Ariza ma\'lumotlari')}}</b></h4>

                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.Ariza sanasi')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ \Carbon\Carbon::parse($user->date)->format('d.m.Y') }}
                                            </span>
                                                </div>
                                            </div>

                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.Tayorlangan shaxobcha yoki sexning nomi')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->prepared)->name  }}
                                            </span>
                                                </div>
                                            </div>
                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.Sertifikatlanuvchi mahsulot')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops->name)->name  }}
                                            </span>
                                                </div>
                                            </div>

                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.Kod TN VED')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->kodtnved  }}
                                            </span>
                                                </div>
                                            </div>
                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.Toʼda (partiya) raqami')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->party_number  }}
                                            </span>
                                                </div>
                                            </div>
                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.amount')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->amount_name  }}
                                            </span>
                                                </div>
                                            </div>
                                            <div class="table_row row">
                                                <div class="col-md-5 col-sm-12 table_td">
                                                    <b>{{trans('app.Hosil yili')}}</b>
                                                </div>
                                                <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($user->crops)->year  }}
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="panel panel-primary">
                                                <div class="tab_wrapper page-tab">
                                                    <ul class="tab_list">
                                                        <h5><b>{{trans('app.Buyurtmachi korxona yoki tashkilot ma\'lumotlari')}}</b></h5>
                                                        <li class="btn-primary">
                                                            <a class="text-light" href="{!! url('/organization/list/edit/'.$company->id) !!}">
                                                                <span class="visible-xs"></span>
                                                                <i class="fa fa-edit">&nbsp;</i>
                                                                <b >{{trans('app.Edit')}}</b>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12 right_side">
                                                            <div class="table_row row">
                                                                <div class="col-md-7 col-sm-12 table_td">
                                                                    <b>{{trans('app.Buyurtmachi korxona yoki tashkilot  STIRi')}}</b>
                                                                </div>
                                                                <div class="col-md-5 col-sm-12 table_td">
                                            <span class="txt_color">
                                            {{ $company->inn }}
                                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="table_row row">
                                                                <div class="col-md-7 col-sm-12 table_td">
                                                                    <b>{{trans('app.Korxona nomi')}}</b>
                                                                </div>
                                                                <div class="col-md-5 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $company->name }}
                                            </span>
                                                                </div>
                                                            </div>

                                                            <div class="table_row row">
                                                                <div class="col-md-7 col-sm-12 table_td">
                                                                    <b>{{trans('app.Address')}}</b>
                                                                </div>
                                                                <div class="col-md-5 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ optional($company->city)->region->name .' '.optional($company->city)->name . ' ' .$company->address  }}
                                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="table_row row">
                                                                <div class="col-md-7 col-sm-12 table_td">
                                                                    <b>{{trans('app.Raxbarning ismi-sharifi')}}</b>
                                                                </div>
                                                                <div class="col-md-5 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $company->owner_name  }}
                                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="table_row row">
                                                                <div class="col-md-7 col-sm-12 table_td">
                                                                    <b>{{trans('app.Mobile No')}}</b>
                                                                </div>
                                                                <div class="col-md-5 col-sm-12 table_td">
                                            <span class="txt_color">
                                                {{ $company->phone_number  }}
                                            </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
        @else
            <div class="section" role="main">
                <div class="card">
                    <div class="card-body text-center">
                        <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
                    </div>
                </div>
            </div>
    @endcan
                <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
                <script>
                    $('body').on('click', '.sa-warning', function() {

                        var url =$(this).attr('url');


                        swal({
                            title: "Haqiqatdan ham tasdiqlashni istaysizmi?",
                            text: "Tasdiqlash uchun barcha ma'lumotlar to'g'riligiga ishonchiz komilmi!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#297FCA",
                            confirmButtonText: "Tasdiqlash!",
                            cancelButtonText: "Tasdiqlashni bekor qilish",
                            closeOnConfirm: false
                        }).then((result) => {
                            window.location.href = url;

                        });
                    });

                </script>
@endsection
