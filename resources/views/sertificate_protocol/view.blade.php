@extends('layouts.app')
@section('styles')
    <style>
        .first-table td {
            width: 20%;
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
        }
        table, th, td{
           border: 1px solid black ;
        }
        td{
            font-weight: normal ;
        }
        .comments-section {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .line {
            border-bottom: 1px solid black;
            margin: 25px 0;
        }
    </style>
@endsection
@section('content')
    @can('view', \App\Models\User::class)
        <div class=" content-area ">
            <div class="page-header">
                <h4 class="page-title mb-0" style="color:white">{{ trans('app.Sinov bayonnomasi') }}</h4>
            </div>

            <div class="panel panel-primary">
                <div class="tab_wrapper page-tab">
                    <ul class="tab_list">
                        <li class="btn-warning">
                            <a class="text-light" href="{{  url()->previous() }}">
                                <span class="visible-xs"></span>
                                <i class="fa fa-arrow-left">&nbsp;</i> {{trans('app.Orqaga')}}
                            </a>
                        </li>
                        <li class="btn-success">
                            <a class="text-light sa-warning" url="{!! url('/sertificate-protocol/change/'. $test->id)!!}">
                                <span class="visible-xs"></span>
                                <i class="fa fa-check fa-lg">&nbsp;</i> Tasdiqlash
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card p-4">
                        @foreach($labResults as $group)
                            @php $i = $loop->iteration - 1; @endphp

                            @include('sertificate_protocol._cheque')

                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="section" role="main">
            <div class="card">
                <div class="card-body text-center">
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('scripts')
    <script>
        $('body').on('click', '.sa-warning', function() {

            var url = $(this).attr('url');


            @if(auth()->user()->id == optional($test->laboratory_final_results)->director_id or auth()->user()->id == 1)
            swal({
                title: "Haqiqatdan ham tasdiqlashni xohlaysizmi?",
                text: "Tasdiqlangandan so'ng ma'lumotlarni o'zgartirib bo'lmaydi!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#297FCA",
                confirmButtonText: "Tasdiqlash!",
                cancelButtonText: "Bekor qilish",
                closeOnConfirm: false
            }).then((result) => {
                window.location.href = url;
            });
            @else
            swal({
                type: "error",
                title: "Xatolik...",
                text: "Sizda tasdiqlash huquqi mavjud emas!",
            });
            @endif
        });
    </script>

@endsection
