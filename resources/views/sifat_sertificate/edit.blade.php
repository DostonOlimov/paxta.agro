@extends('layouts.front')
@section('content')
    <link href="{{ asset('assets/css/formApplications.css') }}" rel="stylesheet">
    <style>
        .table_row {
            display: flex;
            justify-content: space-between;
            border: 1px solid #dedede;
            float: left;
            width: 100%;
            padding: 1px 0px 4px 2px;
        }

        .table_row .table_td {
            padding: 8px 8px !important;
        }

        .txt_color a:visited {
            color: blue !important;
        }
    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-file mr-1"></i> {{trans('app.Ariza Ma\'lumotlari')}}
                </li>
            </ol>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <ul class="tab_list">
                                <li class="tab_item">
                                    <a class="tab_link-back"
                                       href="{{ url('/sifat-sertificates/list') }}">
                                        <i class="fas fa-arrow-left"></i> {{trans('app.Orqaga')}}
                                    </a>
                                </li>
                                <li class="tab_item">
                                    <a class="tab_link" href="{!! url('/sifat-sertificates/edit-data/' . $data->id) !!}">
                                        <i class="fas fa-edit"></i> {{ trans('app.Edit') }}
                                    </a>

                                </li>
                            </ul>
                        </div>
                        <div class="row view-body">
                            <div class="col-md-12 col-sm-12">
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Ariza sanasi</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                            {{ $data->date }}
                                        </span>
                                    </div>
                                </div>
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Mahsulotni tayyorlagan zavod yoki sexning nomi</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                            {{ optional($data->prepared)->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Mahsulot nomi</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                            {{ optional($data->crops)->name->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Kod TN VED</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                            {{ optional($data->crops)->kodtnved }}
                                        </span>
                                    </div>
                                </div>
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Partiya raqami</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                                {{ optional($data->crops)->party_number }}

                                        </span>
                                    </div>
                                </div>
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Dublikat raqami</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                                {{ optional($data->crops)->party2 }}

                                        </span>
                                    </div>
                                </div>
                                <div class="table_row row">
                                    <div class="col-md-5 col-sm-12 table_td">
                                        <b>Miqdori</b>
                                    </div>
                                    <div class="col-md-7 col-sm-12 table_td">
                                        <span class="txt_color">
                                            {{ optional($data->crops)->amount }}
                                            {{ \App\Models\CropData::getMeasureType(optional($data->crops)->measure_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-primary">
                                    <ul class="tab_list">
                                        <li class="tab_item">
                                            <a class="tab_link" href="{!! url('/organization/my-organization-edit/' . $data->organization_id) !!}">
                                                <i class="fas fa-edit"></i> {{ trans('app.Edit') }}
                                            </a>
                                        </li>
                                        <h5> Buyurtmachi korxona yoki tashkilot ma'lumotlari</h5>
                                    </ul>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-12 right_side">
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Tashkilot STIRi</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                                        <span class="txt_color">
                                                            {{ $data->organization->inn }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Tashkilot nomi</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                                        <span class="txt_color">
                                                            {{ $data->organization->name }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Tashkilot manzili</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                                        <span class="txt_color">
                                                            {{ optional($data->organization)->full_address }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Tashkilot rahbari</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                                        <span class="txt_color">
                                                            {{ optional($data->organization)->owner_name }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="table_row row">
                                                    <div class="col-md-5 col-sm-12 table_td">
                                                        <b>Tashkilot telefon raqami</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-12 table_td">
                                                        <span class="txt_color">
                                                            {{ optional($data->organization)->phone_number }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection
