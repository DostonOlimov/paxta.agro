@extends('layouts.front')
@section('content')
    <?php $userid = Auth::user()->id; ?>
    @if(Auth::user()->role == \App\Models\User::ROLE_CUSTOMER)
        <ul class="step-wizard-list ">
            <li class="step-wizard-item">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">Buyurtmachi korxonani qo'shish</span>
            </li>
            <li class="step-wizard-item ">
                <span class="progress-count">2</span>
                <span class="progress-label">Ariza turini tanlash</span>
            </li>
            <li class="step-wizard-item  ">
                <span class="progress-count ">3</span>
                <span class="progress-label">Ariza ma'lumotlarini kiritish</span>
            </li>
            <li class="step-wizard-item  current-item">
                <span class="progress-count last-progress-bar">4</span>
                <span class="progress-label">Zaruriy hujjatlarni yuklash</span>
            </li>
        </ul>
        <div class="section">


            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form action="{{ url('/application/my-file-old-store') }}" method="post"
                          enctype="multipart/form-data" data-parsley-validate
                          class="form-horizontal form-label-left">
                        <input type="hidden" name="app_id" value="{{$app_id}}">
                        <div class="row">
                            <div class="col-md-6 pb-4">
                                <div class="form-control-file">
                                    <label class="form-label">Avvalda rasmiylashtirilgan Muvofiqlik sertifikati</label>
                                    <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                           required name="old_certificate"
                                           accept="application/pdf"
                                    />
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="visibility: hidden;">label</label>
                            <div class="form-group">
                                <a class="btn btn-primary"
                                   href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                <button type="submit"
                                        class="btn btn-success">{{ trans('app.Submit')}}</button>
                            </div>
                        </div>
                    </form>
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
            <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
            <script type="text/javascript">
                function disableButton() {
                    var button = document.getElementById('submitter');
                    button.disabled = true;
                    button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
                    setTimeout(function() {
                        button.disabled = false;
                        button.innerText = 'Saqlash'; // Restore the button text
                    }, 1000);
                }
            </script>
@endsection
