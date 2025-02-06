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
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app.Laboratoriya ma\'lumolari')}}
                    </li>
                </ol>
            </div>
            {{--      start of message component --}}
            <x-flash-message />
        {{--      end of message component --}}

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
                                        <th>{{trans("app.Hudud nomi")}}</th>
                                        <th>{{trans("app.Faylni yuklagan xodim")}}</th>
                                        <th>{{trans("app.Oxirgi yangilangan sanasi")}}</th>
                                        <th>{{trans("app.Ma'lumot miqdori")}}</th>
                                        @if(session('crop') != 4)
                                            <th>HVI ma'lumotlari</th>
                                        @endif
                                        <th>Lclass ma'lumotlari</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($states as $state)
                                        @php if($loop->iteration == 14) continue; @endphp
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td>{{ __('message.' . $state->name) }}</td>
                                            <td>{{ optional(optional($state->hvi_file)->user)->name }} {{ optional(optional($state->hvi_file)->user)->lastname }}</td>
                                            <td>{{ optional($state->hvi_file)->date }}</td>
                                            <td>{{ optional($state->hvi_file)->count }}</td>
                                            @if(session('crop') != 4)
                                                <td>
                                                    <a href="{!! url('/hvi/add/'.$state->id) !!}"><button type="button" class="btn btn-round btn-success"><i class="fa fa-refresh"></i>{{trans('app.Yangilash')}}</button></a>
                                                    <a href="{!! url('/hvi/view/'.$state->id) !!}"><button type="button" class="btn btn-round btn-info"><i class="fa fa-eye"></i>{{ trans('app.View')}}</button></a>
                                                </td>
                                            @endif
                                            <td>
                                                <a href="{!! url('/hvi/add2/'.$state->id) !!}"><button type="button" class="btn btn-round btn-warning"><i class="fa fa-plus"></i> {{trans('app.Qo\'shish')}}</button></a>
                                            </td>
                                        </tr>
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
