@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\User::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>Laboratoriya natijalarini o'zgartirish
                    </li>
                </ol>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="panel panel-primary">
                                <div class="tab_wrapper page-tab">
                                    <ul class="tab_list">
                                        <li>
                                            <a href="{!! url()->previous()!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Orqaga')}}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-pencil fa-lg">&nbsp;</i>
                                            <b>{{ trans('app.Edit')}}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="invoice-form" method="post" action="{!! url('laboratory_results/update') !!}" enctype="multipart/form-data"
                                  data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row" >
                                    @csrf
                                    <input type="hidden"  name="id" value="{{ $apps->laboratory_result->id }}" >

                                    <table class="table table-bordered " style="margin-top:20px;" >
                                        <thead style="text-align: center">
                                        <tr>
                                            <th colspan="2">Ko‘rsatkichlar nomi (talablar)</th>
                                            <th>Sinash usullari MX</th>
                                            <th>Me’yoriy hujjat bo‘yicha me’yoriy ko‘rsatkichlar</th>
                                            <th>Xaqiqiy ko‘rsatkichlar</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php $tr = 1; @endphp
                                        @foreach($indicators as $k => $indicator)
                                            @php
                                                $sum = 0;
                                                $resultValue = '';
                                                    // Check the name of the indicator and assign the corresponding result value
                                                if ($indicator->id == 9) {
                                                    $resultValue = $apps->laboratory_result->mic;
                                                } elseif ($indicator->id == 10) {
                                                    $resultValue = $apps->laboratory_result->staple;
                                                } elseif ($indicator->id == 11) {
                                                        $resultValue = $apps->laboratory_result->strength;
                                                } elseif ($indicator->id == 12) {
                                                        $resultValue = $apps->laboratory_result->uniform;
                                                } elseif ($indicator->id == 13) {
                                                        $resultValue = $apps->laboratory_result->fiblength;
                                                }
                                            @endphp
                                            <tr>
                                                <td>@if(!$indicator->parent_id) {{$tr}} @endif</td>
                                                <td>
                                                    {{$indicator->name}}
                                                </td>
                                                <td>
                                                    {{ $indicator->nd_name }}
                                                </td>
                                                <td>
                                                    @if($indicator->comment)
                                                        {{$indicator->comment}}
                                                    @else
                                                        @if($indicator->value != 0)
                                                            @if($indicator->measure_type == 1) ko'pi bilan, @else kamida @endif
                                                            {{ $indicator->value }}
                                                        @else
                                                            ruxsat etilmaydi
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($indicator->nd_name)
                                                        <input class="form-control" step="0.001" value={{$resultValue}}  name="value{{$indicator->id}}" required>
                                                    @endif
                                                </td>

                                            </tr>
                                            @if(!$indicator->parent_id) @php $tr=$tr+1; @endphp @endif
                                        @endforeach
                                        </tbody>
                                    </table>


                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                    <button type="submit" onclick="disableButton()" id="submitter" class="btn btn-success">{{ trans('app.Submit')}}</button>
                                </div>
                            </form>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $("input.date").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView:'decade',
            endDate: new Date(),
        });

        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
            setTimeout(function() {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 3000);
        }
    </script>
@endsection

