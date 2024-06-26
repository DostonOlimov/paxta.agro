@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@can('viewAny', \App\Models\Application::class)
   <div class="section">
			<!-- PAGE-HEADER -->
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app.Arizalar ro\'yxati')}}
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
						<div class="panel panel-primary">
							<div class="tab_wrapper page-tab">
								<ul class="tab_list">
										<li class="active">
											<a href="{!! url('/application/list')!!}">
												<span class="visible-xs"></span>
												<i class="fa fa-list fa-lg">&nbsp;</i>
												 {{ trans('app.Ro\'yxat')}}
											</a>
										</li>
										<li>
											<a href="{!! url('/application/add')!!}">
												<span class="visible-xs"></span>
												<i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
												{{ trans('app.Qo\'shish')}}</b>
											</a>
										</li>
									</ul>
							</div>
						</div>

{{--                        <!-- filter component -->--}}
                        <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till"  />
{{--                        <!--filter component -->--}}

						<div class="table-responsive">
							<table class="table table-striped table-bordered nowrap display" style="margin-top:20px;" >
								<thead>
									<tr>
                                        <th>#</th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('listapplication', ['sort_by' => 'status', 'sort_order' => ($sort_by === 'status' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{ trans('app.Ariza statusi') }}
                                                @if($sort_by === 'status')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
										<th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('listapplication', ['sort_by' => 'id', 'sort_order' => ($sort_by === 'id' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{trans('app.Ariza raqami')}}
                                                @if($sort_by === 'id')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
                                        <th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('listapplication', ['sort_by' => 'date', 'sort_order' => ($sort_by === 'date' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{trans('app.Ariza sanasi')}}
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
                                            <a href="{{ route('listapplication', ['sort_by' => 'organization', 'sort_order' => ($sort_by === 'organization' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
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
										<th class="border-bottom-0 border-top-0">{{trans('app.Sertifikatlanuvchi mahsulot')}}</th>
										<th class="border-bottom-0 border-top-0">
                                            <a href="{{ route('listapplication', ['sort_by' => 'amount', 'sort_order' => ($sort_by === 'amount' && $sort_order === 'asc') ? 'desc' : 'asc']) }}">
                                                {{trans('app.amount')}}
                                                @if($sort_by === 'amount')
                                                    @if($sort_order === 'asc')
                                                        <i class="fa fa-arrow-up"></i>
                                                    @else
                                                        <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                @endif
                                            </a>
                                        </th>
										<th class="border-bottom-0 border-top-0">{{trans('app.Hosil yili')}}</th>
                                        <th class="border-bottom-0 border-top-0">{{trans('app.Action')}}</th>
									</tr>
								</thead>
								<tbody>
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
                                        <td> <a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->date }}</a></td>
                                        <td><a href="{!! url('/organization/view/'.$app->organization_id) !!}">{{ optional($app->organization)->name }}</a></td>
										<td>{{ optional($app->crops->name)->name }}</td>
										<td>{{ optional($app->crops)->amount_name }}</td>
                                        <td>{{ optional($app->crops)->year }}</td>
										<td>
                                            <a href="{!! url('/application/view/'.$app->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
                                            <a href="{!! url('/application/edit/'.$app->id) !!}" ><button type="button" class="btn btn-round btn-success">{{ trans('app.Edit')}}</button></a>
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
