@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="bi bi-people-fill mr-1"></i>&nbsp Laboratoriya xulosasini yuklash
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
                                        <a href="{!! url('/akt_laboratory/search')!!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-plus-circle">&nbsp;</i>
                                        <b>{{ trans('app.Qo\'shish')}}</b>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <form method="post" action="{!! url('akt_laboratory/store') !!}" enctype="multipart/form-data"  class="form-horizontal upperform">
                            <div class="row">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                <input type="hidden" name="id" value="{{$test->id}}">
                                <div class="col-md-6 form-group has-feedback">
                                    <label class="form-label" for="file">Iclass.dbf faylini yuklang <label class="text-danger">*</label></label>
                                    <div class="form-control">
                                        <input name="file" type="file" class="form-control-file" required >
                                    </div>
                                </div>
                                <div class="form-group col-md-6 form-group has-feedback">
                                    <label class="form-label" for="file">&nbsp;</label>

                                        <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                        <button type="submit" onclick="disableButton()"  class="btn btn-success" id="submitter">{{ trans('app.Submit')}}</button>

                                </div>
                            </div>
                            <div class="row">
                                <h2 class="form-control" style="text-align: center"><b>Shtrix kodlar</b></h2>
                                @foreach($test->gin_balles as $ball)
                                <div class="col-md-3 form-group has-feedback">
                                    <div class="form-control">
                                        <input name="text" type="text" readonly class="form-control" value="{{$ball->from_number}}">
                                    </div>
                                </div>
                                <div class="col-md-3 form-group has-feedback">
                                    <div class="form-control">
                                        <input name="text" type="text" readonly class="form-control" value="{{$ball->to_number}}">
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
            setTimeout(function() {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 10000);
        }
    </script>

@endsection
