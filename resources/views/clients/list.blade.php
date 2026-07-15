@extends('layouts.app')
@section('content')
<?php $user = Auth::user(); ?>

<div class="section">
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-users mr-1"></i>&nbsp;{{ trans('app.Clients') }}
            </li>
        </ol>
    </div>

    <x-flash-message />

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="panel panel-primary">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list">
                                <li class="active">
                                    <a href="{{ url('/clients/list') }}">
                                        <i class="fa fa-list fa-lg">&nbsp;</i>
                                        {{ trans('app.Ro\'yxat') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/clients/add') }}">
                                        <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                        <b>{{ trans('app.Qo\'shish') }}</b>
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
                                    <th>{{ trans('app.Name') }}</th>
                                    <th>{{ trans('app.Code') }}</th>
                                    <th>{{ trans('app.State') }}</th>
                                    <th>{{ trans('app.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $i => $client)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->kod }}</td>
                                    <td>{{ optional($client->state)->name }}</td>
                                    <td>
                                        <a href="{{ url('/clients/list/edit/' . $client->id) }}">
                                            <button type="button" class="btn btn-round btn-success">{{ trans('app.Edit') }}</button>
                                        </a>
                                        <a url="{{ url('/clients/list/delete/' . $client->id) }}" class="sa-warning">
                                            <button type="button" class="btn btn-round btn-danger">{{ trans('app.Delete') }}</button>
                                        </a>
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

<script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
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
