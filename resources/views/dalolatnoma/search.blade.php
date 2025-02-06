@extends('layouts.app')
@section('content')
    @php
        $sortService = new \App\Services\SortService('dalolatnoma.search');
    @endphp
    <!-- page content -->
    @can('viewAny',\App\Models\User::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('message.Na\'muna olish dalolatnomalari')}}
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
                                <table class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{!! $sortService->sortable('id', 'app.Ariza raqami') !!}</th>
                                        <th>{!! $sortService->sortable('party_number', 'app.Toʼda (partiya) raqami') !!}</th>
                                        <th>{!! $sortService->sortable('date', 'app.Ariza sanasi') !!}</th>
                                        <th>{{ trans('app.Viloyat nomi') }}</th>
                                        <th>{!! $sortService->sortable('organization', 'app.Buyurtmachi korxona yoki tashkilot nomi') !!}</th>
                                        <th>{{ trans('app.Sertifikatlanuvchi mahsulot') }}</th>
                                        <th>{{ trans('app.Hosil yili') }}</th>
                                        <th>{{ trans('app.Action') }}</th>
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
                                                <input type="text" name="partyNumber[lk]" class="search-input form-control"
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
                                                                @if (isset($filterValues['stateId']) && $filterValues['stateId'] == $name->id)
                                                                selected
                                                            @endif>
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
                                                                @if (isset($filterValues['nameId']) && $filterValues['nameId'] == $name->id)
                                                                selected
                                                            @endif>
                                                            {{ $name->name }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <select class="w-100 form-control" name="year" id="year">
                                                <option value="" selected>Hosil yilini tanlang</option>
                                                @foreach ($years as $key => $name)
                                                    <option value="{{ $key }}"
                                                            @if (isset($filterValues['year']) && $filterValues['year'] == $key)
                                                            selected
                                                        @endif>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($apps as $app)
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td> <a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->id }}</a></td>
                                            <td>{{ optional($app->crops)->party_number }}</td>
                                            <td> <a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->date }}</a></td>
                                            <td> {{ optional(optional(optional($app->organization)->area)->region)->name }}</td>
                                            <td><a href="#" class="company-link" data-id="{{ $app->organization_id }}">{{ optional($app->organization)->name }}</a></td>
                                            <td>{{ optional($app->crops->name)->name }}</td>
                                            <td>{{ optional($app->crops)->year }}</td>
                                            <td>
                                            <?php $appid=Auth::User()->id; ?>
                                                @if($result = optional($app->tests)->dalolatnoma)
                                                    <a href="{!! url('/dalolatnoma/view/'. $result->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
                                                    <a href="{!! url('/dalolatnoma/edit/'. $result->id) !!}"><button type="button" class="btn btn-round btn-warning">{{ trans('app.Edit')}}</button></a>
                                                @else
                                                    <a href="{!! url('/dalolatnoma/add/'. optional($app->tests)->id) !!}"><button type="button" class="btn btn-round btn-success">&nbsp;Dalolatnoma kiritish &nbsp;</button></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{$apps->links()}}
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
@endsection
@section('scripts')
    <script>
        var translations = {
            inputTooShort: '{{ trans('app.Korxona (nomi), STIR ini kiritib izlang') }}',
            searching: '{{ trans('app.Izlanmoqda...') }}',
            noResults: '{{ trans('app.Natija topilmadi') }}',
            errorLoading: '{{ trans('app.Natija topilmadi') }}',
            placeholder: '{{ trans('app.Korxona nomini kiriting') }}'
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
