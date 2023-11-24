@extends('layouts.front')
@section('content')
    <style>
        .checkbox-success {
            background-color: #cad0cc !important;
            color: red;
        }
    </style>
    <?php $userid = Auth::user()->id; ?>
    @if(Auth::user()->role == \App\Models\User::ROLE_CUSTOMER)
        <ul class="step-wizard-list ">
            <li class="step-wizard-item">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">Buyurtmachi korxonani qo'shish</span>
            </li>
            <li class="step-wizard-item current-item ">
                <span class="progress-count">2</span>
                <span class="progress-label">Ariza turini tanlash</span>
            </li>
            <li class="step-wizard-item ">
                <span class="progress-count">3</span>
                <span class="progress-label">Ariza ma'lumotlarini kiritish</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count last-progress-bar">4</span>
                <span class="progress-label">Zaruriy hujjatlarni yuklash</span>
            </li>
        </ul>
            <div class="section">
                <div class="page-header1">
                    <ol class="breadcrumb">

                    </ol>
                </div>


                            <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <form action="{{ url('/prepared/my-prepared-store') }}" method="post"
                                              enctype="multipart/form-data" data-parsley-validate
                                              class="form-horizontal form-label-left">
                                            <input type="hidden" name="organization" value="{{$organization_id}}">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group overflow-hidden">
                                                        <label class="form-label">Ariza turi<label class="text-danger">*</label></label>
                                                        <select class="w-100 form-control" name="app_type" required >
                                                            @if(count($type))
                                                                <option value="">Ariza turini tanlang</option>
                                                            @endif
                                                            @foreach($type as $key=>$name)
                                                                <option value="{{ $key }}" @if($key == old('app_type')) selected @endif
                                                                > {{$name}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="first-name">  Urug‘lik tayorlangan shaxobcha yoki sex nomi <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <input type="text" required="required" name="name"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="first-name">Shaxobcha yoki sex joylashgan viloyat nomi <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <select name="region" class="w-100 form-control" required="">
                                                            @if($regions && count($regions))
                                                                <option value="">Viloyat tanlang...</option>
                                                            @endif
                                                            @if(!empty($regions))
                                                                @foreach($regions as $region)
                                                                    <option
                                                                        value="{{ $region->id }}">{{ $region->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                                <div class="col-12 col-md-12 text-center">
                                                    <label class="form-label" style="visibility: hidden;">label</label>
                                                    <div class="form-group r">
                                                        <a class="btn btn-primary"
                                                           href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                                        <button type="submit"
                                                                class="btn btn-success">{{ trans('app.Submit')}}</button>
                                                    </div>
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
        $(document).ready(function () {
            $('.region').select2({
                minimumResultsForSearch: Infinity
            });
        })
    </script>
@endsection
