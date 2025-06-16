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
        .header__title {
            font-size: 24px;
            text-align: center;
            margin-top: 1px;
            text-transform: uppercase;
        }

        .header__intro {
            display: flex;
            justify-content: center;
            margin: 0 auto;
            text-align: center;
            font-size: 24px;
            max-width: 90%;
            line-height: 1.6;
        }

        .main__intro {
            display: flex;
            margin: 0 auto;
            text-align: left;
            font-size: 20px;
            line-height: 1.6;
        }
        .head__title{
            font-weight: bold;
            color:#0a52de;
            font-size: 24px;
            margin:0;
            text-align: center;
        }
        .background_image {
            position: absolute;
            top: 55%;
            left: 32%;
            transform: translate(-50%, -50%); /* Center the image */
            width: 550px;
            height: auto;
            opacity: 0.1; /* Adjust the opacity as needed */
            z-index: -1;
        }
    </style>
@endsection
@section('content')
    @can('view', \App\Models\User::class)
        <div class=" content-area ">
            <div class="page-header">
                <h4 class="page-title mb-0" style="color:white">Sifat sertifikati</h4>
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
                            <a class="text-light sa-warning" url="{!! url('/sertificate-protocol/accept', $dalolatnoma)!!}">
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
                            @php $index = $loop->iteration - 1; @endphp

                            @include('sertificate_protocol._sertificate_cheque')

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


            @if(auth()->user()->id == optional($dalolatnoma->laboratory_final_results)->director_id  or auth()->user()->id == 1)
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
    <script>
        $(document).ready(function() {
            function fillCheque() {
                $('#application-date').text(moment(currenttest.laboratory_final_results.date).format('DD.MM.YYYY'))
            }

            function printCheque() {
                $('#invoice-cheque').print({
                    NoPrintSelector: '.no-print',
                    title: '',
                })
            }

            let currenttest = @json($dalolatnoma);


            fillCheque()

            $('#print-invoice-btn').click(function(ev) {
                printCheque()
            })
        });
    </script>
@endsection
