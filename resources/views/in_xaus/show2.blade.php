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
                                        <a class="text-light" href="{{  url('/in_xaus/view/'.$id) }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-arrow-left">&nbsp;</i> {{trans('app.Orqaga')}}
                                        </a>
                                    </li>
                                    <li class="btn-primary">
                                        <a class="text-light" href="{!! url('/in_xaus/list')!!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="examples1" class="table table-bordered nowrap " style="margin-top:20px; font-size: 20px;font-weight: bold" >
                                <thead>
                                <tr style="background-color: #12ba12;">
                                    <th style=" color: white">#</th>
                                    <th  style=" color: white">Qiymati</th>
                                    <th  style=" color: white">Farq</th>
                                    <th  style=" color: white">Farqning kvadrati</th>
                                    <th  style=" color: white">Umumiy farq</th>
                                    <th style=" color: white">Natija</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $offset = (request()->get('page', 1) - 1) * 50;
                                    $sum = 0;
                                @endphp

                                @foreach($values as $value)
                                    <tr>
                                        <td style="background-color: yellow">{{$offset + $loop->iteration}}</td>
                                        <td>{{ $value->value }}</td>
                                        <td>{{ round($value->value - $avg_value,4)}}</td>
                                        <td>{{ round(($value->value - $avg_value) * ($value->value - $avg_value),6)  }}</td>
                                        @php $sum += round(($value->value - $avg_value) * ($value->value - $avg_value),6) @endphp
                                @endforeach
                                        <td style="background-color: #b744e8; color: white;text-align: center;" rowspan="3">{{$sum}}</td>
                                        <td style="background-color: #b744e8; color: white;text-align: center;" rowspan="3">{{round(sqrt($sum/90),6)}}</td>
                                    </tr>
                                <tr>
                                    <td style="background-color: yellow">O'rtacha</td>
                                    <td style="background-color: #b744e8; color: white;text-align: center;" colspan="3">{{round($avg_value,4)}}</td>
                                </tr>
                                    <tr>
                                        <td style="background-color: yellow">Kengaytirish</td>
                                        <td style="background-color: #b744e8; color: white;text-align: center;" colspan="3">{{round( 2 *  sqrt($sum/90),3)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
@endsection
