@extends('layouts.front')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/sertificate.css') }}" type="text/css">

    <!-- page content -->
    <div class="section" style="margin-top: 140px;">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i> Shartnomalar ro'yxati
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
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li class="active">
                                        <a href="{!! url('/sifat-contracts/list') !!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{!! url('/sifat-contracts/add') !!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                {{ trans('app.Qo\'shish') }}</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered nowrap display" style="margin-top:20px;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="border-bottom-0 border-top-0">
                                        <a
                                            href="{{ route('sifat_contracts.list', ['sort_by' => 'number', 'sort_order' => $sort_by === 'number' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                            Shartnoma raqami
                                            @if ($sort_by === 'number')
                                                @if ($sort_order === 'asc')
                                                    <i class="fa fa-arrow-up"></i>
                                                @else
                                                    <i class="fa fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th>Korxona nomi</th>
                                    <th class="border-bottom-0 border-top-0">
                                        <a
                                            href="{{ route('sifat_contracts.list', ['sort_by' => 'inn', 'sort_order' => $sort_by === 'inn' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                            Korxona STIRi
                                            @if ($sort_by === 'inn')
                                                @if ($sort_order === 'asc')
                                                    <i class="fa fa-arrow-up"></i>
                                                @else
                                                    <i class="fa fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-bottom-0 border-top-0">Shartnoma fayli</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr style="background-color: #90aec6 !important;">
                                    <td> </td>
                                    <td>
                                        <form class="d-flex">
                                            <input type="text" name="number[eq]"
                                                   class="search-input form-control"
                                                   value="{{ isset($filterValues['number']) ? $filterValues['number'] : '' }}">
                                        </form>
                                    </td>
                                    <td></td>
                                    <td>
                                        <form class="d-flex">
                                            <input type="text" name="inn[eq]"
                                                   class="search-input form-control"
                                                   value="{{ isset($filterValues['inn']) ? $filterValues['inn'] : '' }}">
                                        </form>
                                    </td>
                                    <td></td>
                                </tr>
                                @php
                                    $offset = (request()->get('page', 1) - 1) * 50;
                                @endphp

                                @foreach ($apps as $app)
                                    <tr>
                                        <td>{{ $offset + $loop->iteration }}</td>
                                        <td>{{ $app->number }}</td>
                                        <td>{{ optional($app->organization)->name }}</td>
                                        <td>{{ optional($app->organization)->inn }}</td>
                                        <td> <span class="txt_color">
                                                @if($app->attachment)
                                                    <a href="{{route('attachment.download', ['id' => $app->attachment->id])}}" class="text-azure">
                                                        <i class="fa fa-download"></i> Sertifikat fayli
                                                                                    </a>
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $apps->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order])->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const labels = {
            inn: @json(trans('app.Tashkilot STIRi')),
            owner: @json(trans('app.Tashkilot rahbari')),
            phone: @json(trans('app.Telefon raqami')),
            address: @json(trans('app.Address')),
            state: @json(trans('app.Viloyat nomi')),
            city: @json(trans('app.Tuman nomi'))
        };
    </script>
    <script src="{{ asset('js/my_js_files/filter.js') }}"></script>
    <script src="{{ asset('js/my_js_files/view_company.js') }}"></script>
@endsection
