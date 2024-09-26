@extends('layouts.app')
@section('content')
    @can('view', \App\Models\User::class)
        <div class=" content-area ">
            <div class="page-header">
                <h4 class="page-title mb-0" style="color:white">Laboratoriya natijalari</h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card p-4">
                        <table class="table table-bordered " style="margin-top:20px;" >
                            <thead style="text-align: center">
                            <tr>
                                <th colspan="2">Ko‘rsatkichlar nomi (talablar)</th>
                                <th>Sinash usullari MX</th>
                                <th>Me’yoriy hujjat bo‘yicha me’yoriy ko‘rsatkichlar</th>
                                <th>Xaqiqiy ko‘rsatkichlar</th>
                                <th>Sinov natijasi</th>
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
                                        @if($resultValue)
                                            {{ $resultValue }}
                                        @else
                                            Aniqlanmadi
                                        @endif
                                    </td>
                                    <td>
                                        @if($indicator->id != 10)
                                            @if($resultValue <= $indicator->value)
                                                Muvofiq
                                            @else
                                                Novufiq
                                            @endif
                                        @endif
                                    </td>

                                </tr>
                                @if(!$indicator->parent_id) @php $tr=$tr+1; @endphp @endif
                            @endforeach
                            </tbody>
                        </table>

                        <div class="py-3">
                            <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{trans("app.Ortga")}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="section" role="main">
            <div class="card">
                <div class="card-body text-center">
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan
@endsection

