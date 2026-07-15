@extends('layouts.app')
@section('content')

<div class="section">
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-users mr-1"></i>&nbsp;{{ trans('app.Edit Client') }}
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="panel panel-primary">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list">
                                <li>
                                    <a href="{{ url('/clients/list') }}">
                                        <i class="fa fa-list fa-lg">&nbsp;</i>
                                        {{ trans('app.Ro\'yxat') }}
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="{{ url('/clients/list/edit/' . $client->id) }}">
                                        <i class="fa fa-pencil fa-lg">&nbsp;</i>
                                        <b>{{ trans('app.Tahrirlash') }}</b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <form action="{{ url('/clients/list/edit/update/' . $client->id) }}" method="post" class="form-horizontal form-label-left">
                                {{ csrf_field() }}
                                <div class="row">

                                    <div class="col-md-4 form-group">
                                        <label class="form-label">{{ trans('app.Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" required value="{{ $client->name }}">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label class="form-label">Korxona stir <span class="text-danger">*</span></label>
                                        <input type="text" name="kod" class="form-control" required
                                               minlength="9" maxlength="9" pattern="\d{9}"
                                               title="9 ta raqam kiritilishi shart"
                                               value="{{ $client->kod }}">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label class="form-label">{{ trans('app.State') }} <span class="text-danger">*</span></label>
                                        <select name="state_id" class="form-control select2" required>
                                            <option value="">{{ trans('app.Select State') }}</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}" {{ $client->state_id == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel') }}</a>
                                        <button type="submit" class="btn btn-success">{{ trans('app.Update') }}</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('.select2').select2({ minimumResultsForSearch: 5 });
});
</script>
@endsection
