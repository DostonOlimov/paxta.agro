@extends('layouts.app')
@section('content')
<!-- page content -->
@php $userid = auth()->id(); @endphp
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
                        <div class="d-flex align-items-center flex-wrap mb-3 px-3 py-2"
                             style="background:#f4f6fb; border-radius:10px; border:1px solid #e3e6f0;">
                            <span class="mr-3 text-muted" style="font-size:13px; font-weight:600; letter-spacing:.3px; white-space:nowrap;">
                                <i class="fa fa-filter mr-1"></i>{{ trans('app.Crop Branch') }}
                            </span>
                            <a href="{{ url('/employee/list') }}"
                               class="btn btn-sm mr-1 {{ !$cropBranchId ? 'btn-secondary' : 'btn-outline-secondary' }}"
                               style="border-radius:50px; font-weight:500;">
                                <i class="fa fa-th-list mr-1"></i>{{ trans('app.All') }}
                            </a>
                            <a href="{{ url('/employee/list') }}?crop_branch={{ \App\Models\User::CROP_BRANCH_TOLA }}"
                               class="btn btn-sm mr-1 {{ $cropBranchId == \App\Models\User::CROP_BRANCH_TOLA ? 'btn-success' : 'btn-outline-success' }}"
                               style="border-radius:50px; font-weight:500;">
                                <i class="fa fa-leaf mr-1"></i>{{ trans('app.Tola') }}
                            </a>
                            <a href="{{ url('/employee/list') }}?crop_branch={{ \App\Models\User::CROP_BRANCH_CHIGIT }}"
                               class="btn btn-sm mr-1 {{ $cropBranchId == \App\Models\User::CROP_BRANCH_CHIGIT ? 'btn-warning' : 'btn-outline-warning' }}"
                               style="border-radius:50px; font-weight:500;">
                                <i class="fa fa-circle mr-1"></i>{{ trans('app.Chigit') }}
                            </a>
                            <a href="{{ url('/employee/list') }}?crop_branch={{ \App\Models\User::CROP_BRANCH_BOTH }}"
                               class="btn btn-sm {{ $cropBranchId == \App\Models\User::CROP_BRANCH_BOTH ? 'btn-info' : 'btn-outline-info' }}"
                               style="border-radius:50px; font-weight:500;">
                                <i class="fa fa-random mr-1"></i>{{ trans('app.Crop Branch Both') }}
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('app.API Token') }}</th>
                                        <th>{{ trans('app.Image') }}</th>
                                        <th>{{ trans('app.First Name') }}</th>
                                        <th>{{ trans('app.Last Name') }}</th>
                                        <th>{{ trans('app.Position') }}</th>
                                        <th>{{ trans('app.Branch') }}</th>
                                        <th>{{ trans('app.Crop Branch') }}</th>
                                        <th>{{ trans('app.Email') }}</th>
                                        <th>{{ trans('app.Mobile No') }}</th>
                                        <th>{{ trans('app.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($user->api_token)
                                                <button class="copy-btn btn btn-primary btn-sm" data-token="{{ $user->api_token }}">
                                                    {{ trans('app.Copy API Token') }}
                                                </button>
                                            @endif
                                        </td>
                                        <td><img src="{{ URL::asset('public/employee/'.$user->image) }}" width="50" height="50" class="img-circle"></td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->lastname }}</td>
                                        <td>{{ $user->position }}</td>
                                        <td>
                                            @if($user->branch_id == \App\Models\User::BRANCH_MAIN)
                                                <span class="badge badge-dark">{{ trans('app.Branch Main') }}</span>
                                            @elseif($user->branch_id == \App\Models\User::BRANCH_STATE)
                                                <span class="badge badge-primary">{{ trans('app.Branch State') }}</span>
                                            @elseif($user->branch_id == \App\Models\User::BRANCH_AREA)
                                                <span class="badge badge-secondary">{{ trans('app.Branch Area') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->crop_branch == \App\Models\User::CROP_BRANCH_TOLA)
                                                <span class="badge badge-success">{{ trans('app.Tola') }}</span>
                                            @elseif($user->crop_branch == \App\Models\User::CROP_BRANCH_CHIGIT)
                                                <span class="badge badge-warning">{{ trans('app.Chigit') }}</span>
                                            @elseif($user->crop_branch == \App\Models\User::CROP_BRANCH_BOTH)
                                                <span class="badge badge-info">{{ trans('app.Crop Branch Both') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->mobile_no }}</td>
                                        <td>
                                            @if(CheckAdmin()=='yes')
                                                <a href="{!! url('/employee/view/'.$user->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View') }}</button></a>
                                                <a href="{!! url('/employee/edit/'.$user->id) !!}"><button type="button" class="btn btn-round btn-success">{{ trans('app.Edit') }}</button></a>
                                                <a url="{!! url('/employee/list/delete/'.$user->id) !!}" class="sa-warning"><button type="button" class="btn btn-round btn-danger">{{ trans('app.Delete') }}</button></a>
                                            @else
                                                <a href="{!! url('/employee/view/'.$user->id) !!}"><button type="button" class="btn btn-round btn-info">{{ trans('app.View') }}</button></a>
                                            @endif
                                        </td>
                                    </tr>
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
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const token = button.getAttribute('data-token');
                const message = '{{ trans('app.API Token Copied') }}';
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(token).then(function () {
                        alert(message);
                    });
                } else {
                    const textArea = document.createElement('textarea');
                    textArea.value = token;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        alert(message);
                    } catch (err) {
                        console.error('Unable to copy:', err);
                    }
                    document.body.removeChild(textArea);
                }
            });
        });
    });
</script>

<script>
    $('body').on('click', '.sa-warning', function () {
        var url = $(this).attr('url');
        swal({
            title: "{{ trans('app.Delete Confirm Title') }}",
            text: "{{ trans('app.Delete Confirm Text') }}",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#297FCA",
            confirmButtonText: "{{ trans('app.Delete Confirm Button') }}",
            cancelButtonText: "{{ trans('app.Delete Cancel Button') }}",
            closeOnConfirm: false
        }).then((result) => {
            window.location.href = url;
        });
    });
</script>

@endsection
