@extends('layouts.app')
@section('styles')
    <style>
        .form-group label {
            display: inline-block;
            margin-bottom: 0; /* Optionally remove any bottom margin */
        }
        .form-group input {
            display: inline-block;
            width: calc(100% - 40px); /* Adjust the width as needed */
            margin-left: 10px; /* Add some space between the label and input */
        }
    </style>
@endsection

@section('content')
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
                                            <a class="text-light" href="{{  url('/in_xaus/list') }}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-arrow-left">&nbsp;</i> {{trans('app.Orqaga')}}
                                            </a>
                                        </li>
                                        <li class="btn-primary">
                                            <a class="text-light" href="{!! url('/in_xaus/edit/'.$user->id)!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-edit fa-lg">&nbsp;</i> {{ trans('app.Edit')}}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 has-feedback pb-1" >
                                    <a class="btn btn-danger" style="width: 100%" href="{!! url('/in_xaus/view2/1/'.$user->id)!!}">{{ "Mikroner"}}</a>
                                </div>
                                <div class="col-md-3 has-feedback">
                                    <a class="btn btn-success" style="width: 100%" href="{!! url('/in_xaus/view2/2/'.$user->id)!!}">{{ "Strength"}}</a>
                                </div>
                                <div class="col-md-3 has-feedback">
                                    <a class="btn btn-info" style="width: 100%" href="{!! url('/in_xaus/view2/3/'.$user->id)!!}">{{ "Iniformity"}}</a>
                                </div>
                                <div class="col-md-3 has-feedback">
                                    <a class="btn btn-warning" style="width: 100%" href="{!! url('/in_xaus/view2/4/'.$user->id)!!}">{{ "Length"}}</a>
                                </div>
                                    @for($i = 0; $i < 10; $i++)
                                        <div class="col-md-3 form-group has-feedback ">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" readonly step="0.001" class="form-control" value="{{ $values[1][$i]->value}}"  name="mic{{$i}}" required>
                                        </div>
                                        <div class="col-md-3 form-group has-feedback">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" readonly step="0.001" class="form-control" value="{{ $values[2][$i]->value}}"  name="str{{$i}}" required>
                                        </div>
                                        <div class="col-md-3 form-group has-feedback">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" readonly step="0.001" class="form-control" value="{{ $values[3][$i]->value}}"  name="inf{{$i}}" required>
                                        </div>
                                        <div class="col-md-3 form-group has-feedback">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" readonly step="0.001" class="form-control" value="{{ $values[4][$i]->value}}"  name="len{{$i}}" required>
                                        </div>
                                    @endfor
                            </div>
                        </div>
                    </div>
                </div>

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
