<!DOCTYPE html>
<html>
<head>
    <title>PAXTA MAHSULOTLARINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN TIZIMI</title>
    <style>
        <style>
        @font-face {
            font-family: 'DejaVuSans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}');
        }
        body {
            font-family: 'DejaVuSans', sans-serif;
        }
        h1 {
            line-height: 1;
            text-align: center;
            font-size: 12px;
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
            font-size: 10px;
        }

        /* QR code styling */
        /* .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-section h2 {
            font-size: 12px;
            font-weight: bold;
        } */
    </style>
    @yield('styles')
</head>

@yield('content')

</html>
