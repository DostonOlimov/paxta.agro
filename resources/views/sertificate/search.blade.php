@extends('layouts.app')
@section('content')
    <!-- page content -->
    @php
        $sortService = new \App\Services\SortService('measurement_mistake.search');
    @endphp
    @can('viewAny',\App\Models\User::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Sertifikatlar ro'yxati
                    </li>
                </ol>
            </div>
            {{--      start of message component --}}
            <x-flash-message />
            {{--      end of message component --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="application" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th>Sertifikat idsi</th>
                                        <th>Sertifikat reestr raqami</th>
                                        <th>Berilgan sanasi</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($tests as $test)
                                        <tr>
                                            <td>{{ $test->id }}</td>
                                            <td>{{ $test->reestr_number }}</td>
                                            <td>{{ $test->given_date }}</td>

                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                    </tbody>
                                </table>
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
