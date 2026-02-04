@extends('layouts.pdf')
@section('styles')
   <style>
     @page {
        margin: 0.3cm 1.2cm; /* top/bottom left/right */
    }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 14px;
        line-height: 1.2;
        margin: 0;
        padding: 0;
    }

    #invoice-cheque {
        position: relative;
        width: 100%;
        padding: 12px;
        z-index: 1;
    }

    /* Watermark Background */
    .background_image {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 550px;
        opacity: 0.12;
        transform: translate(-50%, -50%);
        z-index: -1;
    }

    /* Titles */
    h1, h2 {
        margin: 0 0 8px 0;
        padding: 0;
        text-align: center;
        line-height: 1.5;
    }

    h2 {
        font-size: 16px;
        font-weight: bold;
    }

    h1 {
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Tables */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 6px 0;
        font-size: 16px;
    }

    table td {
        padding: 0;
        border: none;
        vertical-align: top;
        font-size: 14px;
    }
      .head_image {
            max-width: 100%;
            /* Optional: To make sure the image is responsive */
            height: 150px;
            padding-left: 60px;
        }

   /* Width helpers */
    .w-65 { width: 65%; }
    .w-35 { width: 35%; }
    .w-33 { width: 33%; }
    .w-34 { width: 34%; }

    /* Alignment classes */
    .left { text-align: left; }
    .right { text-align: right; }
    .center { text-align: center; }

    /* Typography */
    .bold { font-weight: bold; }
    .serif { font-family: 'Times New Roman', serif; }

    /* Underline for fillable-like areas */
    .underline {
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
    }

    /* Signature Section */
    .signature-section {
        margin-top: 25px;
    }

    .qr-container img {
        width: 90px;
        height: auto;
    }

    .director-name {
        font-weight: bold;
        text-align: right;
        padding-right: 10px;
    }

    /* Conclusion rows */
    .conclusion-table td {
        border: none;
        font-size: 14px;
        padding: 8px 0;
        text-align: justify;
    }
     .head__title{
            font-weight: bold;
            color:#0a52de;
            font-size: 20px;
            text-align: center;
            line-height: 1.5;
        }
    .header__title {
            font-size: 16px;
            text-align: center;
            margin-top: 1.2px;
            text-transform: uppercase;
            line-height: normal;
            padding-bottom: 8px;
            padding-top: 12px;
        }
    .head__title2{
            font-weight: bold;
            text-align: center;
            line-height: normal;

        }
</style>

@endsection
@section('content')
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
    @endphp

    @include('product_conclusion._cheque2')

@endsection
