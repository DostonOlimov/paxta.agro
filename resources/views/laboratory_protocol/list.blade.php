@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\User::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans('message.Sinov bayonnomalari') }}
                    </li>
                </ol>
            </div>
            @if (session('message'))
                <div class="row massage">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="alert alert-success text-center">
                            @if (session('message') == 'Successfully Submitted')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Submitted') }}</label>
                            @elseif(session('message') == 'Successfully Updated')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Updated') }} </label>
                            @elseif(session('message') == 'Successfully Deleted')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Deleted') }} </label>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till" />
            <!--filter component -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="examples1" class="table table-striped table-bordered nowrap"
                                    style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0 border-top-0">#</th>
                                            <th class="border-bottom-0 border-top-0">{{ trans("app.To'da (partya) raqami") }}
                                            </th>
                                            <th>{{ trans('app.Sinov dasturi raqami') }}</th>
                                            <th>{{ trans('app.Buyurtmachi tashkilot nomi') }}</th>
                                            <th>{{trans('app.Sertifikatlanuvchi mahsulot')}}</th>
                                            <th>{{trans('app.amount')}}</th>

                                            <th>{{ trans('app.Action') }}</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @php
                                            $offset = (request()->get('page', 1) - 1) * 50;
                                        @endphp
                                        @foreach ($tests as $test)
                                            <tr>
                                                <td>{{ $offset + $loop->iteration }}</td>
                                                <td> {{ optional($test->test_program->application->crops)->party_number }}</td>
                                                <td>{{ optional(optional($test->test_program->application)->decision)->number }}
                                                </td>
                                                <td><a
                                                        href="{!! url('/organization/view/' . $test->test_program->application->organization_id) !!}">{{ $test->test_program->application->organization->name }}</a>
                                                </td>
                                                <td>{{ $test->test_program->application->crops->name->name }}</td>
                                                <td>{{ optional($test->test_program->application)->crops->amount_name }}</td>
                                                <td>
                                                    @if (isset($test->measurement_mistake) && isset($test->laboratory_result) && isset($test->result) && !isset($test->laboratory_final_results))
                                                        <a href="{!! url('laboratory-protocol/add/' . $test->id) !!}"> <button type="button"
                                                            class="btn btn-round btn-warning "><i
                                                                class="fa fa-plus-circle"></i>
                                                            {{ trans('app.Qo\'shish') }}</button></a>
                                                    @elseif (isset($test->measurement_mistake) && isset($test->laboratory_result) && isset($test->result) && isset($test->laboratory_final_results))
                                                                <a href="{!! url('laboratory-protocol/view/' . $test->id) !!}"><button type="button"
                                                                    class="btn btn-round btn-info">
                                                                    {{ trans('app.View') }}</button></a>
                                                            @if ($test->laboratory_final_results->status == 0)
                                                                <a href="{!! url('laboratory-protocol/change/' . $test->id) !!}"><button type="button"
                                                                        class="btn btn-round btn-success">{{ trans('app.Tasdiqlash') }}</button></a>
                                                            @elseif ($test->laboratory_final_results->status == 1)
                                                                <button type="button"
                                                                    class="btn btn-round btn-danger">{{ trans('app.Tasdiqlangan') }}</button>
                                                            @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $tests->links() }}
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan
    <!-- /page content -->
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>

    <script>
        $('body').on('click', '.sa-warning', function() {

            var url = $(this).attr('url');


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