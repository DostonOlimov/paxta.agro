@extends('layouts.front')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@if(Auth::user()->role == \App\Models\User::ROLE_CUSTOMER)
   <div class="section">
			<!-- PAGE-HEADER -->
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fa fa-paste mr-1"></i>&nbsp Mening arizalarim
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
											<a href="{!! url('/application/my-applications')!!}">
												<span class="visible-xs"></span>
												<i class="fa fa-list fa-lg">&nbsp;</i>
												 Arizalarim
											</a>
										</li>
										<li>
											<a href="{!! url('/organization/my-organization-add')!!}">
												<span class="visible-xs"></span>
												<i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
												Ariza berish
											</a>
										</li>
									</ul>
							</div>
						</div>


						<div class="table-responsive">
							<table id="examples1" class="table table-striped table-bordered nowrap display" style="margin-top:20px;" >
								<thead>
									<tr>
                                        <th class="border-bottom-0 border-top-0">#</th>
                                        <th class="border-bottom-0 border-top-0">Status</th>
                                        <th class="border-bottom-0 border-top-0">Ariza raqami</th>
                                        <th class="border-bottom-0 border-top-0">Ariza sanasi</th>
										<th class="border-bottom-0 border-top-0">Buyurtmachi korxona yoki tashkilot nomi</th>
										<th class="border-bottom-0 border-top-0">Ekin turi</th>
										<th class="border-bottom-0 border-top-0">Ekin navi</th>
										<th class="border-bottom-0 border-top-0">Ekin miqdori</th>
										<th class="border-bottom-0 border-top-0">Hosil yili</th>
                                        <th class="border-bottom-0 border-top-0">Action</th>

									</tr>
								</thead>
								<tbody>
                                @php
                                    $offset = (request()->get('page', 1) - 1) * 50;
                                @endphp

									@foreach($apps as $app)
									<tr>
                                        <td>{{$offset + $loop->iteration}}</td>
                                        <td><button type="button" class="btn btn-round btn-{{$app->status_color}}">{{ $app->status_name}}</button></td>
                                        <td> <a href="{!! url('/application/my-application-view/'.$app->id) !!}">{{ $app->app_number == 0 ? '-' : $app->app_number }}</a></td>
                                        <td>{{ $app->date }}</td>
                                        <td><a href="{!! url('/organization/my-organization-view/'.$app->organization_id) !!}">{{ optional($app->organization)->name }}</a></td>
										<td>{{ optional($app->crops->name)->name }}</td>
										<td>{{ optional($app->crops->type)->name }}</td>
										<td>{{ optional($app->crops)->amount_name }}</td>
                                        <td>{{ optional($app->crops)->year }}</td>

										<td>
                                            <a href="{!! url('/application/my-application-view/'.$app->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
                                            <a url="{!! url('/application/my-application-delete/'.$app->id)!!}" class="sa-warning"> <button type="button" class="btn btn-round btn-danger dgr">{{ trans('app.Delete')}}</button></a>
                                        </td>
									</tr>
								@endforeach
								</tbody>
                    	</table>
                            {{ $apps->links() }}
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
@endif
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
