@extends('layouts.front')
@section('content')
    <style>
        .txt_color a:visited{
            color:blue !important;
        }
    </style>
    <?php $userid = Auth::user()->id; ?>
    @if()
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fa fa-files-o mr-1"></i>&nbsp Talab etilgan hujjatlarni o'zgartirish
                    </li>
                </ol>
            </div>
            <div class="row massage">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-warning text-center">
                        <label for="checkbox-10 text-bold">Faqat o'zgartirmoqchi bo'lgan faylingiz ostiga fayl yuklang aks holda o'zgartirishsiz qoldiring!</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form action="{{ url('/application/my-file-old-update') }}" method="post"
                          enctype="multipart/form-data" data-parsley-validate
                          class="form-horizontal form-label-left">
                        <input type="hidden" name="file_id" value="{{$files->id}}">
                        <div class="row">
                            <div class="col-md-6 pb-4">
                                <div class="form-control-file">
                                    <label class="form-label">Avvalda rasmiylashtirilgan muvofiqlik sertifikati
                                        <span class="txt_color">
                                            @if($files->certificate)
                                                <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->old_certificate_file) }}" ><i class="fa fa-download"></i> Dalolatnoma fayli</a>
                                            @else
                                                Fayl yuklanmagan
                                            @endif
                                            </span></label>
                                    <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                           name="certificate"
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
