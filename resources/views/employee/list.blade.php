@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@if (getAccessStatusUser('Employees',$userid)=='yes')
   <div class="section">
			<!-- PAGE-HEADER -->
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans('app.Employees')}}
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
											<a href="{!! url('/employee/list')!!}">
												<span class="visible-xs"></span>
												<i class="fa fa-list fa-lg">&nbsp;</i>
												 {{ trans('app.Ro\'yxat')}}
											</a>
										</li>
										<li>
											<a href="{!! url('/employee/add')!!}">
												<span class="visible-xs"></span>
												<i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
												{{ trans('app.Qo\'shish')}}</b>
											</a>
										</li>
									</ul>
							</div>
						</div>
						<div class="table-responsive">
							<table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
								<thead>


									<tr>
										<th>#</th>
                                        <th>API Token</th>
										<th>{{ trans('app.Image')}}</th>
										<th>{{ trans('app.First Name')}}</th>
										<th>{{ trans('app.Last Name')}}</th>
										<th>Lavozim</th>
										<th>{{ trans('app.Email')}}</th>
										<th>{{ trans('app.Mobile Number')}}</th>
										<th>{{ trans('app.Action')}}</th>
									</tr>

								</thead>
								<tbody>
									<?php $i = 1; ?>
									@foreach($users as $user)
									<tr>
										<td>{{ $i }}</td>
                                        <td>
                                            @if ($user->api_token)
                                                <button class="copy-btn btn btn-primary" data-token="{{ $user->api_token }}">
                                                    Copy to API
                                                </button>
                                            @endif
                                        </td>
										<td><img src="{{ URL::asset('public/employee/'.$user->image) }}"  width="50px" height="50px" class="img-circle"></td>
										<td>{{ $user->name }}</td>
										<td>{{ $user->lastname }}</td>
										<td>{{ $user->position }}</td>
                                        <td>{{ $user->email }}</td>
										<td>{{ $user->mobile_no }}</td>
										<td>

											<?php $userid=Auth::User()->id; ?>
											@if(CheckAdmin($userid)=='yes')
												<a href="{!! url('/employee/view/'.$user->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>
												<a href="{!! url('/employee/edit/'.$user->id) !!}" ><button type="button" class="btn btn-round btn-success">{{ trans('app.Edit')}}</button></a>
												<a url="{!! url('/employee/list/delete/'.$user->id) !!}" class="sa-warning"><button type="button" class="btn btn-round btn-danger">{{ trans('app.Delete')}}</button></a>
											@else
												<a href="{!! url('/employee/view/'.$user->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View')}}</button></a>

											@endif
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
 <!-- /page content -->
<script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function copyTextToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                alert('API token copied to clipboard!');
            } catch (err) {
                console.error('Unable to copy:', err);
            }
            document.body.removeChild(textArea);
        }

        document.querySelectorAll('.copy-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const token = button.getAttribute('data-token');
                copyTextToClipboard(token);
            });
        });
    });
</script>

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
