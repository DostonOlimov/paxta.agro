@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@can('viewAny', \App\Models\User::class)

    <div class="section">
			<!-- PAGE-HEADER -->
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('message.Klassiyorlar')}}
				</li>
			</ol>
		</div>
        @if(session('message'))
            <div class="row massage">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert @php echo (session('message')=='Cannot Deleted' or session('message')=='Duplicate Data') ? 'alert-danger' : 'alert-success' @endphp text-center">
                        @if(session('message') == 'Successfully Submitted')
                            <label for="checkbox-10 colo_success"> {{trans('app.Successfully Submitted')}}  </label>
                        @elseif(session('message')=='Successfully Updated')
                            <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Updated')}}  </label>
                        @elseif(session('message')=='Successfully Deleted')
                            <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Deleted')}}  </label>
                        @elseif(session('message')=='Duplicate Data')
                            <label for="checkbox-10"> {{ trans('app.Duplicate Data')}}  </label>
                        @elseif(session('message')=='Cannot Deleted')
                            <label for="checkbox-10 "> {{ trans('app.Cannot Deleted')}}  </label>
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
												<a href="{!! url('/klassiyor/list')!!}">
													<span class="visible-xs"></span>
													<i class="fa fa-list fa-lg">&nbsp;</i>
													 {{ trans('app.Ro\'yxat')}}
												</a>
											</li>
											<li>
												<a href="{!! url('/klassiyor/add')!!}">
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
										<th>{{trans('app.Klassiyor nomi')}}</th>
										<th>{{trans('app.Klassiyor kodi')}}</th>
										<th>{{ trans('app.Viloyat nomi')}}</th>
										<th>{{ trans('app.Action')}}</th>
									</tr>
								</thead>
								<tbody>
								<?php $i=1;?>
								 @foreach($klassiyors as $klassiyor)
									<tr>
										<td>{{ $i }}</td>
										<td>{{ $klassiyor->name }}</td>
										<td>{{ $klassiyor->kode }}</td>
										<td>{{ optional($klassiyor->laboratory)->city->region->name }}</td>
										<td>
											<a href="{!! url ('/klassiyor/list/edit/'.$klassiyor->id) !!}"> <button type="button" class="btn btn-round btn-success">{{ trans('app.Edit')}}</button></a>

											<a url="{!! url('/klassiyor/list/delete/'.$klassiyor->id)!!}" class="sa-warning"> <button type="button" class="btn btn-round btn-danger dgr">{{ trans('app.Delete')}}</button></a>
										</td>
									</tr>
								<?php $i++; ?>
								@endforeach
								</tbody>
							</table>
                                {{$klassiyors->links()}}
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