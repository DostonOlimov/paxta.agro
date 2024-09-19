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
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('message.O\'lchash xatoligini aniqlash dalolatnomasi')}}
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
                                            <a href="{{ route('humidity_result.search', ['sort_by' => 'number', 'sort_order' => ($sort_by === 'number' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Dalolatnoma raqami') }}
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
                                            <a href="{{ route('humidity_result.search', ['sort_by' => 'party_number', 'sort_order' => ($sort_by === 'party_number' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
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
                                            <a href="{{ route('humidity_result.search', ['sort_by' => 'date', 'sort_order' => ($sort_by === 'date' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
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
                                        <th class="border-bottom-0 border-top-0">{{trans('app.Viloyat nomi')}}</th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('humidity_result.search', ['sort_by' => 'organization', 'sort_order' => ($sort_by === 'organization' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}}
                                                @if($sort_by === 'organization')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            {{ trans('app.Zavod nomi va kodi') }}
                                        </th>
                                        <th>{{trans('app.Sertifikatlanuvchi mahsulot')}}</th>
                                        <th>{{trans('app.Action')}}</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <tr style="background-color: #90aec6 !important;">
                                    <td></td>
                                    <td>
                                        <form class="d-flex">
                                            <input type="text" name="number[eq]" class="search-input form-control"
                                                   value="{{ isset($filterValues['number']) ? $filterValues['number'] : '' }}">
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
                                    <td></td>
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
                                    <td></td>
                                    </tr>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($apps as $app)
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td>{{ $app->number }}</td>
                                            <td> {{ optional($app->test_program->application->crops)->party_number }}</td>
                                            <td> {{ $app->date }}</td>
                                            <td> {{ optional(optional(optional($app->test_program->application->organization)->area)->region)->name }}</td>
                                            <td><a href="#" class="company-link" data-id="{{ $app->test_program->application->organization_id }}">{{ optional($app->test_program->application->organization)->name }}</a></td>
                                            <td>{{ optional($app->test_program)->application->prepared->name }} - {{ optional($app->test_program)->application->prepared->kod }}</td>
                                            <td>{{ optional($app->test_program)->application->crops->name->name }}</td>
                                            <td>
                                                <?php $appid=Auth::User()->id; ?>
                                                @if($app->humidity_result)
                                                    @if($result = $app->measurement_mistake)
                                                        <a href="{!! url('/measurement_mistake/view/'. $result->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
                                                        <a href="{!! url('/measurement_mistake/edit/'. $result->id) !!}"><button type="button" class="btn btn-round btn-warning">{{ trans('app.Edit')}}</button></a>
                                                    @else
                                                        <a href="{!! url('/measurement_mistake/add/'. $app->id) !!}"><button type="button" class="btn btn-round btn-success">&nbsp;Dalolatnomani kiritish &nbsp;</button></a>
                                                    @endif
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
