@extends('layouts.pdf')
@section('styles')
    <style>
        .invoice-cheque {
            width: 100% !important;
            margin: 0 auto;
            font-size: 16px;
            height: 100vh; /* Ensure full height of the page */
            overflow: hidden; /* Prevent content from spilling over */
        }

        .header__title {
            font-size: 16px;
            text-align: center;
            margin-top: 1px;
            text-transform: uppercase;
        }

        .header__intro {
            display: flex;
            justify-content: center;
            margin: 0 auto;
            text-align: center;
            font-size: 16px;
            max-width: 90%;
            line-height: 1.3;
        }

        .main__intro {
            display: flex;
            justify-content: center;
            margin: 0 auto;
            text-align: left;
            font-size: 16px;
            max-width: 100%;
            line-height: 1.6;
        }

        h1 {
            line-height: 1.6;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        h2 {
            font-weight: normal;
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        table th,
        table td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
        }

        table th {
            font-weight: bold;
        }

        table td {
            font-size: 14px;
        }

        .container {
            display: flex;
            justify-content: center;
            /* Horizontal centering */
            align-items: center;
            /* Vertical centering */
            height: 100vh;
            /* Full viewport height or adjust accordingly */
        }

        img {
            max-width: 100%;
            /* Optional: To make sure the image is responsive */
            height: 150px;
            padding-left: 125px;
        }

        .text-center img {
            max-width: 100px;
            /* Restrict QR code width */
            margin-top: auto;
            /* Push the QR code to the bottom of the div */
        }

        #invoice-cheque {
            position: relative;
            width: 100%;
            height: 100vh;
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
        .content {
            position: relative;
            z-index: 1; /* Keeps content above the image */
        }
        .head__title{
            font-weight: bold;
            color:#0a52de;
            font-size: 24px;
            margin:0;
            text-align: center;
        }
    </style>
@endsection
@section('content')
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
    @endphp

    @include('sertificate_protocol._sertificate_cheque')

@endsection
