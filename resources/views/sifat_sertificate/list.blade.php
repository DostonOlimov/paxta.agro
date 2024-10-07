@extends('layouts.front')
@section('content')
<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        background-color: #f4f6f9;
    }

    .btn-view-success-container {
        display: flex;
        justify-content: center;
        flex-wrap: nowrap;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    table .btn {
        padding: 5px 16px 8px 16px !important;
    }

    .my_header .navbar {
        padding: 16px 0;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }


    .container {
        max-width: 90vw;
        margin: 0 auto;
    }

    .card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 120px;

    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
        overflow: hidden;
    }

    .table thead th {
        vertical-align: middle;
        background-color: #0052cc;
        color: #ffffff !important;
        border: 1px solid #dee2e6;
    }

    th a {
        color: #ffffff !important;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #90aec6;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .search-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        margin: 0;
    }

    .btn-round {
        border-radius: 20px;
        padding: 8px 12px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-success {
        background-color: #0052cc !important;
        color: white;
    }

    .btn-info:hover,
    .btn-success:hover {
        opacity: 0.9;
    }

    .filter-row {
        background-color: #90aec6;
    }

    .filter-row select,
    .filter-row input {
        margin-bottom: 10px;
    }
</style>
<!-- page content -->
<div class="section">
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-life-buoy mr-1"></i> Sifat sertifikati bo'yicha arizalar ro'yxati
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="panel panel-primary">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list">
                                <li class="active">
                                    <a href="{!! url('/sifat-sertificate/list') !!}">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-list fa-lg">&nbsp;</i>
                                        {{ trans('app.Ro\'yxat') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{!! url('/organization/my-organization-add') !!}">
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
                                                href="{{ route('/sifat-sertificates/list', ['sort_by' => 'id', 'sort_order' => $sort_by === 'id' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Ariza raqami') }}
                                                @if ($sort_by === 'id')
                                                    @if ($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a
                                                href="{{ route('/sifat-sertificates/list', ['sort_by' => 'party_number', 'sort_order' => $sort_by === 'party_number' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Toʼda (partiya) raqami') }}
                                                @if ($sort_by === 'party_number')
                                                    @if ($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a
                                                href="{{ route('/sifat-sertificates/list', ['sort_by' => 'date', 'sort_order' => $sort_by === 'date' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Ariza sanasi') }}
                                                @if ($sort_by === 'date')
                                                    @if ($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">{{ trans('app.Viloyat nomi') }}</th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a
                                                href="{{ route('/sifat-sertificates/list', ['sort_by' => 'organization', 'sort_order' => $sort_by === 'organization' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Buyurtmachi korxona yoki tashkilot nomi') }}
                                                @if ($sort_by === 'organization')
                                                    @if ($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>

                                        <th class="border-bottom-0 border-top-0">
                                            {{ trans('app.Sertifikatlanuvchi mahsulot') }}</th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a
                                                href="{{ route('/sifat-sertificates/list', ['sort_by' => 'amount', 'sort_order' => $sort_by === 'amount' && $sort_order === 'asc' ? 'desc' : 'asc']) }}">
                                                {{ trans('app.amount') }}
                                                @if ($sort_by === 'amount')
                                                    @if ($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">{{ trans('app.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background-color: #90aec6 !important;">
                                        <td> </td>
                                        <td>
                                            <form class="d-flex">
                                                <input type="text" name="id[eq]" class="search-input form-control"
                                                    value="{{ isset($filterValues['id']) ? $filterValues['id'] : '' }}">
                                            </form>
                                        </td>
                                        <td>
                                            <form class="d-flex">
                                                <input type="text" name="partyNumber[lk]"
                                                    class="search-input form-control"
                                                    value="{{ isset($filterValues['partyNumber']) ? $filterValues['partyNumber'] : '' }}">
                                            </form>
                                        </td>
                                        <td></td>
                                        <td>
                                            <select class="w-100 form-control name_of_corn custom-select" name="stateId"
                                                id="stateId">
                                                <option value="" selected>Viloyat nomini tanlang</option>
                                                @if (!empty($states))
                                                    @foreach ($states as $name)
                                                        <option value="{{ $name->id }}"
                                                            @if (isset($filterValues['stateId']) && $filterValues['stateId'] == $name->id) selected @endif>
                                                            {{ $name->name }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <select id="organization" class="form-control owner_search" name="organization">
                                                @if (!empty($organization))
                                                    <option selected value="{{ $organization->id }}">
                                                        {{ $organization->name }}</option>
                                                @endif
                                            </select>
                                            @if ($organization)
                                                <i class="fa fa-trash" style="color:red"
                                                    onclick="changeDisplay('companyId[eq]')"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="w-100 form-control name_of_corn custom-select" name="name"
                                                id="crops_name">
                                                @if (count($names))
                                                    <option value="" selected>
                                                        Mahsulot turini tanlang</option>
                                                @endif
                                                @if (!empty($names))
                                                    @foreach ($names as $name)
                                                        <option value="{{ $name->id }}"
                                                            @if (isset($filterValues['nameId']) && $filterValues['nameId'] == $name->id) selected @endif>
                                                            {{ $name->name }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp

                                    @foreach ($apps as $app)
                                        <tr>
                                            <td>{{ $offset + $loop->iteration }}</td>
                                            <td> <a href="{!! url('/sifat-sertificates/view/' . $app->id) !!}">{{ $app->id }}</a></td>
                                            <td>{{ optional($app->crops)->party_number }}</td>
                                            <td> <a href="{!! url('/sifat-sertificates/view/' . $app->id) !!}">{{ $app->date }}</a></td>
                                            <td> {{ optional(optional(optional($app->organization)->area)->region)->name }}
                                            </td>
                                            <td><a href="#" class="company-link"
                                                    data-id="{{ $app->organization_id }}">{{ optional($app->organization)->name }}</a>
                                            </td>
                                            <td>{{ optional($app->crops->name)->name }}</td>
                                            <td>{{ optional($app->crops)->amount_name }}</td>
                                            <td>
                                                <a href="{!! url('/sifat-sertificates/view/' . $app->id) !!}"><button type="button"
                                                        class="btn btn-round btn-info">{{ trans('app.View') }}</button></a>
                                                <a href="{!! url('/sifat-sertificates/edit/' . $app->id) !!}"><button type="button"
                                                        class="btn btn-round btn-success">{{ trans('app.Edit') }}</button></a>
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
    var translations = {
        inputTooShort: '{{ trans('
        app.Korxona(nomi),
        STIR ini kiritib izlang ') }}',
        searching: '{{ trans('
        app.Izlanmoqda...') }}',
        noResults: '{{ trans('
        app.Natija topilmadi ') }}',
        errorLoading: '{{ trans('
        app.Natija topilmadi ') }}',
        placeholder: '{{ trans('
        app.Korxona nomini kiriting ') }}'
    };

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
<script src="{{ asset('js/my_js_files/get_company.js') }}"></script>
<script src="{{ asset('js/my_js_files/view_company.js') }}"></script>
@endsection