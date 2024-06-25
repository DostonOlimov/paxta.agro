@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny',\App\Models\User::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app. Og\'irlik bo\'yicha na\'muna olish dalolatnomalari')}}
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
                                <table class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th class="border-bottom-0 border-top-0">#</th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('akt_amount.search', ['sort_by' => 'number', 'sort_order' => ($sort_by === 'number' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Sinov dasturi raqami') }}
                                                @if($sort_by === 'number')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('akt_amount.search', ['sort_by' => 'party_number', 'sort_order' => ($sort_by === 'party_number' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{ trans('app.To\'da (partya) raqami') }}
                                                @if($sort_by === 'party_number')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('akt_amount.search', ['sort_by' => 'date', 'sort_order' => ($sort_by === 'date' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Dalolatnoma sanasi') }}
                                                @if($sort_by === 'date')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('akt_amount.search', ['sort_by' => 'prepared', 'sort_order' => ($sort_by === 'prepared' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Zavod nomi va kodi') }}
                                                @if($sort_by === 'prepared')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th>{{trans('app.Sertifikatlanuvchi mahsulot')}}</th>
                                        <th class="border-bottom-0 border-top-0">{{ trans('app.Sof og\'irligi (kg)') }}</th>
                                        <th>{{trans('app.Tara og\'irligi(kg)')}}</th>
                                        <th>{{trans('app.Action')}}</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($tests as $test)
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td>{{ optional(optional($test->test_program->application)->decision)->number }}</td>
                                            <td> {{ optional($test->test_program->application->crops)->party_number }}</td>
                                            <td> {{ $test->date }}</td>
                                            <td>{{ optional($test->test_program)->application->prepared->name }} - {{ optional($test->test_program)->application->prepared->kod }}</td>
                                            <td>{{ optional($test->test_program)->application->crops->name->name }}</td>
                                            <td @if(! $test->akt_amount_sum_amount) class="text-danger" @endif>{{ $test->akt_amount_sum_amount ? $test->akt_amount_sum_amount - $test->toy_count * $test->tara : 0 }} kg</td>
                                            <td class="text-center">{{ $test->tara }}<br><a href="{!! url('/dalolatnoma/tara_edit/'.$test->id) !!}"><button type="button" class="btn btn-round btn-success">{{ trans('app.Edit')}}</button></a>
                                            </td>
                                            <td>
                                                <a href="{!! url('/akt_amount/view/'.$test->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
                                                <a href="{!! url('/akt_amount/edit/'.$test->id) !!}"><button type="button" class="btn btn-round btn-warning">{{ trans('app.Edit')}}</button></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{$tests->links()}}
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
                title: "O'chirishni istaysizmi?",
                text: "O'chirilgan ma'lumotlar qayta tiklanmaydi!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#297FCA",
                confirmButtonText: "Ha, o'chirish!",
                cancelButtonText: "O'chirishni bekor qilish",
                closeOnConfirm: false
            }).then((result) => {
                window.location.href = url;

            });
        });

    </script>

@endsection
