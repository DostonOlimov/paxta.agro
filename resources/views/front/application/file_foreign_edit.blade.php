@extends('layouts.front')
@section('content')
    <style>
        .txt_color{
            color:red;
        }
        .txt_color a:visited{
            color:blue !important;
        }
    </style>
     @can('myupdate', $app)
        <div class="section">
            <div class="row massage">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-warning text-center">
                        <label for="checkbox-10 text-bold">Faqat o'zgartirmoqchi bo'lgan faylingiz ostiga fayl yuklang aks holda o'zgartirishsiz qoldiring!</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form action="{{ url('/application/my-file-foreign-update') }}" method="post"
                          enctype="multipart/form-data" data-parsley-validate
                          class="form-horizontal form-label-left">
                        <input type="hidden" name="file_id" value="{{$files->id}}">
                        <div class="row">
                            <div class="col-md-6 pb-4">
                                <div class="form-control-file">
                                    <label class="form-label">Karantin ruxsatnomasi (IKR)
                                        <span class="txt_color text-danger">
                                        @if($files->karantin)
                                            <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->karantin_file) }}" ><i class="fa fa-download"></i> Ruxsatnoma fayli</a>
                                        @else
                                            Fayl yuklanmagan
                                            @endif</span></label>
                                    <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                            name="karantin"
                                           accept="application/pdf"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6 pb-4">
                                <div class="form-control-file">
                                    <label class="form-label">Mahsulotga tegishli xorijiy fitosanitar sertifikati
                                        <span class="txt_color">
                                                @if($files->fitosanitar)
                                                <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->fitosanitar_file) }}" ><i class="fa fa-download"></i> Xulosa fayli</a>
                                            @else
                                                Fayl yuklanmagan
                                            @endif
                                            </span></label>
                                    <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                            name="fitosanitar"
                                           accept="application/pdf"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6 pb-4">
                                <label class="form-label">Muvofiqlik sertifikati
                                    <span class="txt_color">
                                                 @if($files->sertifikat)
                                            <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->sertificat_file) }}" ><i class="fa fa-download"></i> Sertifikat fayli</a>
                                        @else
                                            Fayl yuklanmagan
                                        @endif
                                            </span></label>
                                <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                        name="m_sertificat"
                                       accept="application/pdf"
                                />
                            </div>
                            <div class="col-md-6 pb-4">
                                <label class="form-label">Markirovka
                                    <span class="txt_color">
                                               @if($files->markirovka)
                                            <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->markirovka_file) }}" ><i class="fa fa-download"></i> Markirovka fayli</a>
                                        @else
                                            Fayl yuklanmagan
                                        @endif
                                            </span></label>
                                <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                        name="markirovka"
                                       accept="application/pdf"
                                />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 pb-4 form-label">Zaruriy hollarda</div>
                            <div class="col-md-6 pb-4">
                                <div class="form-control-file">
                                    <label class="form-label">Hisob-faktura (invoys)
                                        <span class="txt_color">
                                                @if($files->invoys)
                                                <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->invoys_file) }}" ><i class="fa fa-download"></i> Invoys fayli</a>
                                            @else
                                                Fayl yuklanmagan
                                            @endif
                                            </span></label>
                                    <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                           name="invoys"
                                           accept="application/pdf"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6 pb-4">
                                <label class="form-label">Yuk xati
                                    <span class="txt_color">
                                                @if($files->yuk_xati)
                                            <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->yuk_xati_file) }}" ><i class="fa fa-download"></i> Yuk xati fayli</a>
                                        @else
                                            Fayl yuklanmagan
                                        @endif
                                            </span></label>
                                <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                       name="yuk_xati"
                                       accept="application/pdf"
                                />
                            </div>
                            <div class="col-md-6 pb-4">
                                <label class="form-label">SMR
                                    <span class="txt_color">
                                                @if($files->smr)
                                            <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($files->smr_file) }}" ><i class="fa fa-download"></i> SMR fayli</a>
                                        @else
                                            Fayl yuklanmagan
                                        @endif
                                            </span></label>
                                <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                       name="smr"
                                       accept="application/pdf"
                                />
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

            @endcan
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
