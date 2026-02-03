@extends('layouts.app')
@section('styles')
 <style>
        @media print {
            body {
                padding: 0;
                background: white;
            }
            #invoice-cheque {
                box-shadow: none;
                page-break-after: always;
            }
            .no-print, .edit-icon, .editable-field {
                display: none !important;
            }
            .editable-wrapper:hover {
                background: transparent !important;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            padding: 20px;
            background: #f5f5f5;
            line-height: 1.4;
        }

        #invoice-cheque {
            background: white;
            padding: 40px 60px;
            max-width: 210mm;
            margin: 0 auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            min-height: 297mm;
            position: relative;
        }

        /* Typography */
        .bold {
            font-weight: bold;
        }

        .serif {
            font-family: 'Times New Roman', serif;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .underline {
            border-bottom: 1px solid #333;
            display: inline-block;
            min-width: 150px;
            padding-bottom: 2px;
        }

        /* Header */
        h2.bold {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 30px;
            line-height: 1.5;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Title */
        h1 {
            text-align: center;
            font-size: 15pt;
            margin: 30px 0;
            line-height: 1.6;
            font-weight: bold;
        }

        h1 .serif {
            font-size: 16pt;
        }

        /* Tables */
        .section-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }

        .section-table td {
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 11pt;
            line-height: 1.5;
        }

        .section-table tr:first-child td {
            padding-top: 8px;
        }

        .section-table .bold {
            font-weight: 600;
        }

        /* Editable Wrapper Styles */
        .editable-wrapper {
            position: relative;
            display: inline-block;
            transition: all 0.2s ease;
            border-radius: 4px;
            padding: 2px 28px 2px 4px;
            margin: -2px -4px;
        }

        .editable-wrapper:hover {
            background-color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .editable-content {
            display: inline;
            min-height: 20px;
        }

        .edit-icon {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 12px;
            padding: 4px 7px;
            opacity: 0;
            transition: all 0.3s ease;
            border: none;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .editable-wrapper:hover .edit-icon {
            opacity: 1;
        }

        .edit-icon:hover {
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 3px 8px rgba(102, 126, 234, 0.4);
        }

        .edit-icon:active {
            transform: translateY(-50%) scale(0.95);
        }

        /* Inline Edit Styles */
        .inline-edit-input {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            padding: 6px 10px;
            border: 2px solid #667eea;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
            min-width: 200px;
        }

        .inline-edit-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        /* Conclusion Text Area */
        .conclusion-table td {
            text-align: justify;
            line-height: 1.8;
            padding: 8px 0;
            position: relative;
        }

        .editable-row {
            position: relative;
        }

        .editable-row .editable-wrapper {
            display: block;
            padding: 8px 40px 8px 8px;
            margin: -8px 0;
        }

        .editable-row .edit-icon {
            right: 8px;
            padding: 6px 10px;
            font-size: 14px;
        }

        .edit-mode textarea {
            width: 100%;
            min-height: 100px;
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.8;
            text-align: justify;
            padding: 12px;
            border: 2px solid #667eea;
            border-radius: 6px;
            resize: vertical;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .edit-mode textarea:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .edit-actions {
            margin-top: 12px;
            text-align: right;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11pt;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
        }

        .btn-save:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background: #e9ecef;
            color: #495057;
        }

        .btn-cancel:hover {
            background: #dee2e6;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Loading indicator */
        .loading {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success/Error messages */
        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 14px 24px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 400px;
        }

        .message.success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .message.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .message::before {
            content: '✓';
            font-size: 18px;
            font-weight: bold;
        }

        .message.error::before {
            content: '✕';
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Final note */
        p {
            margin: 20px 0;
            font-size: 11pt;
            line-height: 1.6;
            text-align: left;
        }

        /* Signature section */
        .signature-section {
            margin-top: 50px;
        }

        .signature-section .section-table td {
            padding: 10px 5px;
            vertical-align: middle;
        }

        .qr-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr-container img {
            display: block;
            margin: 0 auto 5px;
            max-height: 100px;
        }

        .qr-container span {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 5px;
        }

        .director-name {
            font-weight: bold;
            font-size: 11pt;
            line-height: 1.4;
        }

        .specialist-row td {
            padding-top: 15px !important;
        }

        .region-date-table td {
            padding: 10px 0;
            font-size: 11pt;
        }

        .region-date-table .underline {
            min-width: 200px;
        }

        .batch-info-table td {
            border: none;
            padding: 8px 5px;
        }

        /* Inline field edit */
        .inline-edit-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            #invoice-cheque {
                padding: 20px;
            }

            h2.bold {
                font-size: 10pt;
            }

            h1 {
                font-size: 12pt;
            }

            .section-table td {
                font-size: 9pt;
            }
        }
</style>
@endsection
@section('content')
    @can('view', \App\Models\User::class)
    @csrf
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
                            <a class="text-light sa-warning" url="{!! url('/product-conclusion/change', $test)!!}">
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

                            @include('product_conclusion._cheque')

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
        $('body').on('click', '.sa-warning', function () {

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