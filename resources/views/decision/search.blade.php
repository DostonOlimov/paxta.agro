@extends('layouts.app')
@section('content')
    @can('viewAny', \App\Models\User::class)
    <!-- page content -->
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header" >
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" >
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app.Sertifikatlashtirishni oʼtkazish uchun berilgan ariza boʼyicha qaror va sinov dasturlari ro\'yxati')}}
                    </li>
                </ol>
            </div>
            @if(session('message'))
                <div class="row massage">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="alert alert-success text-center">
                            @if(session('message') == 'Successfully Submitted')
                                <label for="checkbox-10 colo_success"> {{trans('app.Successfully Submitted')}}</label>
                            @elseif(session('message')=='Successfully Updated')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Updated')}}  </label>
                            @elseif(session('message')=='Successfully Deleted')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Deleted')}}  </label>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till"  />
            <!--filter component -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th class="border-bottom-0 border-top-0">#</th>
                                        <th>To'da partya raqami</th>
                                        <th>{{trans('app.Ariza sanasi')}}</th>
                                        <th>{{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}}</th>
                                        <th>{{trans('app.Sertifikatlanuvchi mahsulot')}}</th>
                                        <th>{{trans('app.Hosil yili')}}</th>
                                        <th>{{trans('app.Qarorlar')}}</th>
                                        <th>{{trans('app.Sinov dasturlari')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp

                                    @foreach($apps as $app)
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td> {{ optional($app->crops)->party_number }}</td>
                                            <td> <a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->date }}</a></td>
                                            <td><a href="{!! url('/organization/view/'.$app->organization_id) !!}">{{ optional($app->organization)->name }}</a></td>
                                            <td>{{ optional($app->crops->name)->name }}</td>
                                            <td>{{ optional($app->crops)->year }}</td>
                                                @if($descion = $app->decision)
                                                <td>
                                                    <a href="{!! url('/decision/view/'.$descion->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Qaror fayli')}}</button></a>
                                                </td>
                                                <td>
                                                    @if($app->tests)
                                                       <a href="{!! url('/tests/view/'.$app->tests->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sinov dasturi fayli')}}</button></a>
                                                    @endif
                                                </td>
                                                <td>
                                                @if($app->decision->status == \App\Models\Decision::STATUS_NEW)
                                                    <button type="button" class="btn btn-round btn-success sa-warning" url="{{ url('/decision/send/'.$app->id) }}">{{ trans('app.Tasdiqlash')}}</button>
                                                @else
                                                     <button type="button" class="btn btn-round btn-warning">{{ trans('app.Tasdiqlangan')}}</button>
                                                @endif
                                                </td>

                                                @else
                                                <td>
                                                    <a href="{!! url('/decision/add/'.$app->id) !!}"><button type="button" class="btn btn-round btn-success">&nbsp;{{trans('app.Qarorni shakllantirish')}} &nbsp;</button></a>
                                                </td>
                                                <td></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $apps->links() }}
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
    <!-- /page content -->
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>

    <script>
        $('body').on('click', '.sa-warning', function() {

            var url =$(this).attr('url');


            swal({
                title: "Tasdiqlashni istaysizmi?",
                text: "Tasdiqlangandan so'ng ma'lumotlarni o'zgartirib bo'lmaydi!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#297FCA",
                confirmButtonText: "Ha, tasdiqlash!",
                cancelButtonText: "Bekor qilish",
                closeOnConfirm: false
            }).then((result) => {
                window.location.href = url;

            });
        });

    </script>

@endsection
