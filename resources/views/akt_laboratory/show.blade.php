@extends('layouts.app')

@section('content')
    <style>
        th {
            background-color: #2381c5 !important;
            color: white !important;
            font-weight: bold !important;
            text-align: center;
            font-size: 1rem !important;
        }

        table .form-control {
            font-size: 0.9rem !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #eaf2ee;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }


        .table-responsive table {
            transform: rotate(180deg);
            direction: initial;
        }

        .table-responsive nav .pagination {
            padding-top: 13px;
            direction: initial;
            transform: rotate(180deg);
        }

        .right_side .table_row, .member_right .table_row {
            border-bottom: 1px solid #dedede;
            float: left;
            width: 100%;
            padding: 1px 0px 4px 2px;
        }

        .table_row .table_td {
            padding: 8px 8px !important;
        }
        th{
            font-weight: bold;
        }
        td{
            font-weight: bold;
        }
        #top-scroll-btn {
            width: 200px;
            display: none;
            position: fixed;
            bottom: 20px;
            font-size: 18px;
            font-weight: bold;
            background-color: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
        }

        /* Optional: Add a fixed header for the table */
        #examples1 thead {
            position: sticky;
            top: 0;
            background-color: #3498db;
            color: #ffffff;
        }

        #examples1 th, #examples1 td {
            padding: 10px;
            text-align: left;
        }

        .table-responsive {
            transform: rotate(180deg);
            direction: rtl;
        }

        .table-responsive::-webkit-scrollbar {
            transform: rotate(180deg);
            height: 16px;
            background-color: #d4d4d4;
            border-radius: 25px !important;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            cursor: pointer;
            background-color: #0E46A3  !important;
        }


        .table-responsive table {
            transform: rotate(180deg);
            direction: initial;
        }

        .table-responsive nav .pagination {
            padding-top: 13px;
            direction: initial;
            transform: rotate(180deg);
        }
    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp HVI ma'lumotlari
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
                                    <li>
                                        <a href="{!! url('/akt_laboratory/search')!!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-eye fa-lg">&nbsp;</i>
                                        {{ trans('app.View')}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="examples1" class="table table-striped table-bordered " style="margin-top:20px;">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Gin_id</th>
                                                    <th>Gin_bale</th>
                                                    <th>Lot_number</th>
                                                    <th>Og'irlik</th>
                                                    <th>Selection</th>
                                                    <th>Date_recvd</th>
                                                    <th>Time_recvd</th>
                                                    <th>date_hvid</th>
                                                    <th>time_hvid</th>
                                                    <th>date_class</th>
                                                    <th>Time_class</th>
                                                    <th>classer_id</th>
                                                    <th>qual_ctrl</th>
                                                    <th>cutout</th>
                                                    <th>reclass</th>
                                                    <th>times_hvid</th>
                                                    <th>attempts</th>
                                                    <th>status</th>
                                                    <th>correction</th>
                                                    <th>croptype</th>
                                                    <th>firstgrade</th>
                                                    <th>grade</th>
                                                    <th>sort</th>
                                                    <th>class</th>
                                                    <th>staple</th>
                                                    <th>mic</th>
                                                    <th>leaf</th>
                                                    <th>ext_matter</th>
                                                    <th>remarks</th>
                                                    <th>strength</th>
                                                    <th>color_gr</th>
                                                    <th>color_rd</th>
                                                    <th>color_b</th>
                                                    <th>trash</th>
                                                    <th>uniform</th>
                                                    <th>fiblength</th>
                                                    <th>elongation</th>
                                                    <th>sfi</th>
                                                    <th>temperatur</th>
                                                    <th>humidity</th>
                                                    <th>hvi_num</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($results as $result)
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$result->gin_id}}</td>
                                                        <td>{{$result->gin_bale}}</td>
                                                        <td>{{$result->lot_number}}</td>
                                                        <td>{{$result->weight}}</td>
                                                        <td>{{$result->selection}}</td>
                                                        <td>{{$result->date_recvd}}</td>
                                                        <td>{{$result->time_recvd}}</td>
                                                        <td>{{$result->date_hvid}}</td>
                                                        <td>{{$result->time_hvid}}</td>
                                                        <td>{{$result->date_class}}</td>
                                                        <td>{{$result->time_class}}</td>
                                                        <td>{{$result->classer_id}}</td>
                                                        <td>{{$result->qual_ctrl}}</td>
                                                        <td>{{$result->cutout}}</td>

                                                        <td>{{$result->reclass}}</td>
                                                        <td>{{$result->times_hvid}}</td>
                                                        <td>{{$result->attempts}}</td>
                                                        <td>{{$result->status}}</td>
                                                        <td>{{$result->correction}}</td>
                                                        <td>{{$result->croptype}}</td>
                                                        <td>{{$result->firstgrade}}</td>
                                                        <td>{{$result->grade}}</td>
                                                        <td>{{$result->sort}}</td>
                                                        <td>{{$result->class}}</td>
                                                        <td>{{$result->staple}}</td>
                                                        <td>{{$result->mic}}</td>
                                                        <td>{{$result->leaf}}</td>
                                                        <td>{{$result->ext_matter}}</td>
                                                        <td>{{$result->remarks}}</td>
                                                        <td>{{$result->strength}}</td>
                                                        <td>{{$result->color_gr}}</td>
                                                        <td>{{$result->color_rd}}</td>
                                                        <td>{{$result->color_b}}</td>
                                                        <td>{{$result->trash}}</td>
                                                        <td>{{$result->uniform}}</td>
                                                        <td>{{$result->fiblength}}</td>
                                                        <td>{{$result->elongation}}</td>
                                                        <td>{{$result->sfi}}</td>
                                                        <td>{{$result->temperatur}}</td>
                                                        <td>{{$result->humidity}}</td>
                                                        <td>{{$result->hvi_num}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Top Scroll Button -->
                                <button id="top-scroll-btn" onclick="scrollToTop()">Yuqoriga</button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
            <script>
                // JavaScript function to scroll to the top
                function scrollToTop() {
                    document.body.scrollTop = 0;  // For Safari
                    document.documentElement.scrollTop = 0;  // For Chrome, Firefox, IE, and Opera
                }

                // Show/hide the Top Scroll button based on the scroll position
                window.onscroll = function() {
                    showTopScrollButton();
                };

                function showTopScrollButton() {
                    var topScrollBtn = document.getElementById("top-scroll-btn");
                    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                        topScrollBtn.style.display = "block";
                    } else {
                        topScrollBtn.style.display = "none";
                    }
                }
            </script>
@endsection
