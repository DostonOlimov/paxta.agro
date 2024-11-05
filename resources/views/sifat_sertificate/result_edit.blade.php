@extends('layouts.front')
@section('content')
        <style>
            @media screen and (max-width: 768px) {
                main {
                    margin: 135px 0 !important;
                }

                .my_header .navbar {
                    padding: 10px 0;
                    align-items: center;
                }

                .my_header .nav-logo {
                    margin-right: 0px;
                }

                .right-side-header {
                    column-gap: 13px;
                }

                h4 {
                    max-width: 155px;
                    font-size: 14px;
                }
            }
        </style>
        <link href="{{ asset('assets/css/formApplications.css') }}" rel="stylesheet">
        <ul class="step-wizard-list">
            <li class="step-wizard-item">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">{{ trans('app.Buyurtmachi korxonani qo\'shish') }}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">2</span>
                <span class="progress-label">{{ trans('app.Mahsulot ma\'lumotlari') }}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label">{{ trans('app.Yuk ma\'lumotlari') }}</span>
            </li>
            <li class="step-wizard-item current-item">
                <span class="progress-count last-progress-bar">4</span>
                <span class="progress-label">{{ trans('app.Sifat ko\'rsatkichlari') }}</span>
            </li>
        </ul>

        <div class="section">

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="invoice-form" method="post" action="{!! url('sifat-sertificates/result-update') !!}"
                                  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $data->id }}">

                                    <table class="table table-bordered ">
                                        <thead style="text-align: center">
                                        <tr>
                                            <th colspan="2">Ko‘rsatkichlar nomi (talablar)</th>
                                            <th>Sinash usullari MX</th>
                                            <th>Xaqiqiy ko‘rsatkichlar</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php $tr = 1; @endphp
                                        @foreach ($data->chigit_result as $k => $result)
                                            <tr>
                                                <td>
                                                    @if (!optional($result->indicator)->parent_id)
                                                        {{ $tr }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ optional($result->indicator)->name }}
                                                </td>
                                                <td>
                                                    {{ optional($result->indicator)->nd_name }}
                                                </td>

                                                <td>
                                                    @if (optional($result->indicator)->nd_name)
                                                        <input class="form-control" step="0.001"
                                                               name="value{{ optional($result)->id }}" value="{{optional($result)->value}}" required>
                                                    @endif
                                                </td>

                                            </tr>
                                            @if (!optional($result->indicator)->parent_id)
                                                @php $tr=$tr+1; @endphp
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>


                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel') }}</a>
                                    <button type="submit" onclick="disableButton()" id="submitter"
                                            class="btn btn-success">{{ trans('app.Submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
            setTimeout(function() {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 3000);
        }
    </script>
@endsection
