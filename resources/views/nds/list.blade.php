@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@if (getAccessStatusUser('Vehicles',$userid)=='yes')
	 @if(getActiveCustomer($userid)=='yes' || getActiveEmployee($userid)=='yes')

    <div class="section">
			<!-- PAGE-HEADER -->
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans("app.Normativ hujjat nomlari")}}
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
												<a href="{!! url('/nds/list')!!}">
													<span class="visible-xs"></span>
													<i class="fa fa-list fa-lg">&nbsp;</i>
													 {{ trans('app.Ro\'yxat')}}
												</a>
											</li>
											<li>
												<a href="{!! url('/nds/add')!!}">
													<span class="visible-xs"></span>
													<i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
													{{ trans('app.Qo\'shish')}}</b>
												</a>
											</li>
										</ul>
								</div>
							</div>
							<div class="table-responsive">
								<table id="datatable" class="table table-striped table-bordered nowrap" style="margin-top:20px; width:100%;">
								<thead>
									<tr>
										<th>#</th>
										<th>{{trans("app.Hujjat")}}</th>
										<th>{{trans("app.Hujjat nomi")}}</th>
                                        <th>{{trans("app.Uruglik turi")}}</th>
										<th>{{ trans('app.Action')}}</th>
									</tr>
								</thead>
								<tbody>
								<?php $i=1;?>
								 @foreach($nds as $nd)
									<tr>
										<td>{{ $i }}</td>
										<td>{{ $nd->fullname }}</td>
										<td>{{ $nd->name }}</td>
                                        <td>{{ optional($nd->crops)->name }}</td>
										<td>
											<a href="{!! url ('/nds/list/edit/'.$nd->id) !!}"> <button type="button" class="btn btn-round btn-success">{{ trans('app.Edit')}}</button></a>

											<a url="{!! url('/nds/list/delete/'.$nd->id)!!}" class="sa-warning"> <button type="button" class="btn btn-round btn-danger dgr">{{ trans('app.Delete')}}</button></a>
										</td>
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
	@endif
@else
	<div class="section" role="main">
		<div class="card">
			<div class="card-body text-center">
				<span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
			</div>
		</div>
	</div>

@endif
<script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
<!-- delete vehicalbrand -->
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
            cancelButtonText:"O'chirishni bekor qilish",
            closeOnConfirm: false
        }).then((result) => {
      window.location.href = url;

        });
    });

</script>

@endsection
