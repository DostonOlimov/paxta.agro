@extends('layouts.app')
@section('content')
<!-- page content -->
@php
    $sortService = new \App\Services\SortService('application.list');
@endphp

   <div class="section">
			<!-- PAGE-HEADER -->
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>{{trans('app.Arizalar ro\'yxati')}}
				</li>
			</ol>
		</div>

       {{--      start of message component --}}
            <x-flash-message></x-flash-message>
       {{--      end of message component --}}

        <div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
                        @php
                            $tabs = [
                                ['name' => "app.Ro'yxat", 'url' => 'application/list', 'icon' => 'fa-list'],
                                ['name' => "app.Qo'shish", 'url' => 'application/add', 'icon' => 'fa-plus-circle']
                            ];
                        @endphp

                        @include('components.tab-menu', ['tabs' => $tabs])

						<div class="table-responsive">
							<table class="table table-striped table-bordered nowrap display" style="margin-top:20px;" >
								<thead>
                                <tr>
                                    <th>#</th>
                                    <th>{!! $sortService->sortable('status', 'app.Ariza statusi') !!}</th>
                                    <th>{!! $sortService->sortable('id', 'app.Ariza raqami') !!}</th>
                                    <th>{!! $sortService->sortable('party_number', 'app.Toʼda (partiya) raqami') !!}</th>
                                    <th>{!! $sortService->sortable('date', 'app.Ariza sanasi') !!}</th>
                                    <th>{{ trans('app.Viloyat nomi') }}</th>
                                    <th>{!! $sortService->sortable('organization', 'app.Buyurtmachi korxona yoki tashkilot nomi') !!}</th>
                                    <th>{{ trans('app.Sertifikatlanuvchi mahsulot') }}</th>
                                    <th>{!! $sortService->sortable('amount', 'app.amount') !!}</th>
                                    <th>{{ trans('app.Hosil yili') }}</th>
                                    <th>{{ trans('app.Action') }}</th>
                                </tr>
								</thead>
								<tbody>
                                @php
                                $filterArray =
                                    [
                                        [],
                                        ['type' => 'select', 'name' => 'status[eq]','fname' => 'status', 'options' => $all_status, 'placeholder' => 'Barchasi'],
                                        ['type' => 'text', 'name' => 'id[eq]', 'fname' => 'id','placeholder' => 'Ariza raqamini kiriting'],
                                        ['type' => 'text', 'name' => 'partyNumber[lk]','fname' => 'partyNumber', 'placeholder' => 'Partiya raqami'],
                                        [],
                                        ['type' => 'select', 'name' => 'stateId', 'options' => $states->pluck('name', 'id'), 'placeholder' => 'Viloyat nomi'],
                                        ['type' => 'select', 'name' => 'organization', 'options' => [$organization->id ?? 1 => $organization->name ?? ''], 'placeholder' => 'Tashkilot'],
                                        ['type' => 'select', 'name' => 'nameId', 'options' => $names->pluck('name', 'id'), 'placeholder' => 'Mahsulot turi'],
                                        [],
                                        ['type' => 'select', 'name' => 'year', 'options' => $years, 'placeholder' => 'Hosil yili'],
                                        [],
                                    ];
                                @endphp
                                    <x-filter-row
                                        :filters="$filterArray"
                                        :filterValues="$filterValues"></x-filter-row>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                @endphp

									@foreach($apps as $app)
									<tr>
                                        <td>{{$offset + $loop->iteration}}</td>
                                        <td><a href="{!! url('/application/view/' . $app->id) !!}"><button type="button"
                                            class="btn btn-round btn-{{ $app->status_color }}">{{ $app->status_name }}</button></a>
                                        </td>
                                        <td> <a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->id }}</a></td>
                                        <td>{{ optional($app->crops)->party_number }}</td>
                                        <td> <a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->date }}</a></td>
                                        <td> {{ optional(optional(optional($app->organization)->area)->region)->name }}</td>
                                        <td><a href="#" class="company-link" data-id="{{ $app->organization_id }}">{{ optional($app->organization)->name }}</a></td>
										<td>{{ optional($app->crops->name)->name }}</td>
										<td>{{ optional($app->crops)->amount_name }}</td>
                                        <td>{{ optional($app->crops)->year }}</td>
										<td>
                                            <a href="{!! url('/application/view/'. $app->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
                                            <a href="{!! url('/application/edit/'. $app->id) !!}" ><button type="button" class="btn btn-round btn-success">{{ trans('app.Edit')}}</button></a>
										</td>
                                         @if (auth()->user()->id == 1)
                                        <td>
                                            <form action="{{ url('/application/delete/' . $app->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-round btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
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
