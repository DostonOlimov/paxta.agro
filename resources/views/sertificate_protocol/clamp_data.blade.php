@extends('layouts.app')

@section('content')
    <style>
        th {
            background-color: #2381c5 !important;
            color: white !important;
            font-weight: bold !important;
            text-align: center;
            font-size: 1rem !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #eaf2ee;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        td {
            font-weight: bold;
        }

        .info-box {
            background-color: #f4f7fb;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        #top-scroll-btn {
            width: 200px;
            display: none;
            position: fixed;
            bottom: 20px;
            font-size: 18px;
            font-weight: bold;
            background-color: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
        }

        #examples1 thead {
            position: sticky;
            top: 0;
            background-color: #3498db;
            color: #ffffff;
        }

        #examples1 th, #examples1 td {
            padding: 10px;
            text-align: left;
        }

        .table-responsive {
            transform: rotate(180deg);
            direction: rtl;
        }

        .table-responsive::-webkit-scrollbar {
            transform: rotate(180deg);
            height: 16px;
            background-color: #d4d4d4;
            border-radius: 25px !important;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            cursor: pointer;
            background-color: #0E46A3 !important;
        }

        .table-responsive table {
            transform: rotate(180deg);
            direction: initial;
        }
    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ $dalolatnoma->number }} - dalolatnoma HVI ma'lumotlari
                </li>
            </ol>
        </div>
        {{--      start of message component --}}
        <x-flash-message />
        {{--      end of message component --}}

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li>
                                        <a href="{{ route('sertificate_protocol.list') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-eye fa-lg">&nbsp;</i>
                                        {{ trans('app.View')}}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="info-box">
                                    <div class="row">
                                        <div class="col-md-3">
                                            {{ trans('app.Dalolatnoma raqami') }} : <b>{{ $dalolatnoma->number }}</b>
                                        </div>
                                        <div class="col-md-3">
                                            {{ trans('app.Dalolatnoma sanasi') }} : <b>{{ $dalolatnoma->date }}</b>
                                        </div>
                                        <div class="col-md-3">
                                            {{ trans('app.To\'da (partya) raqami') }} :
                                            <b>{{ optional(optional(optional($dalolatnoma->test_program)->application)->crops)->party_number }}</b>
                                        </div>
                                        <div class="col-md-3">
                                            Kip soni : <b>{{ $clampData->count() }} ta</b>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            {{ trans('app.Buyurtmachi korxona yoki tashkilot nomi') }} :
                                            <b>{{ optional(optional(optional($dalolatnoma->test_program)->application)->organization)->name }}</b>
                                        </div>
                                        <div class="col-md-3">
                                            {{ trans('app.Zavod nomi va kodi') }} :
                                            <b>{{ optional(optional(optional($dalolatnoma->test_program)->application)->prepared)->name }}
                                                - {{ optional(optional(optional($dalolatnoma->test_program)->application)->prepared)->kod }}</b>
                                        </div>
                                        <div class="col-md-3">
                                            {{ trans('app.Sertifikatlanuvchi mahsulot') }} :
                                            <b>{{ optional(optional(optional(optional($dalolatnoma->test_program)->application)->crops)->name)->name }}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="info-box">
                                    <form method="post" action="{{ route('hvi.store_dalolatnoma', $dalolatnoma) }}"
                                          enctype="multipart/form-data" class="form-horizontal upperform">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-5 form-group has-feedback">
                                                <label class="form-label" for="file">
                                                    Iclass.dbf faylini yuklang <label class="text-danger">*</label>
                                                </label>
                                                <div class="form-control">
                                                    <input name="file" type="file" class="form-control-file" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3 form-group has-feedback">
                                                <label class="form-label">&nbsp;</label>
                                                <div>
                                                    <button type="submit" onclick="disableButton()"
                                                            class="btn btn-success" id="submitter">
                                                        <i class="fa fa-upload"></i> HVI ma'lumotlarini yuklash
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group has-feedback">
                                                <label class="form-label">Shtrix kodlar</label>
                                                <div class="form-control" style="height: auto;">
                                                    @forelse($dalolatnoma->gin_balles as $ball)
                                                        {{ $ball->from_number }} - {{ $ball->to_number }}<br>
                                                    @empty
                                                        <span class="text-danger">Shtrix kodlar kiritilmagan</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    @if(auth()->user()->isAdmin() && $clampData->isNotEmpty())
                                        <form method="post" action="{{ route('hvi.delete_dalolatnoma', $dalolatnoma) }}"
                                              onsubmit="return confirm('Ushbu dalolatnomaning {{ $clampData->count() }} ta HVI ma\'lumoti o\'chiriladi. Davom etasizmi?');">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa fa-trash"></i> HVI ma'lumotlarini o'chirish
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($averages && $averages->mic)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-box">
                                        <div class="row">
                                            <div class="col-md-2">O'rtacha mic : <b>{{ round($averages->mic, 2) }}</b></div>
                                            <div class="col-md-2">O'rtacha staple : <b>{{ round($averages->staple, 2) }}</b></div>
                                            <div class="col-md-2">O'rtacha strength : <b>{{ round($averages->strength, 2) }}</b></div>
                                            <div class="col-md-2">O'rtacha uniform : <b>{{ round($averages->uniform, 2) }}</b></div>
                                            <div class="col-md-2">O'rtacha fiblength : <b>{{ round($averages->fiblength / 100, 2) }}</b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(count($summary))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <table class="table table-striped table-bordered nowrap">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nav (sort)</th>
                                                    <th>Sinf (class)</th>
                                                    <th>Kip soni</th>
                                                    <th>Og'irlik (kg)</th>
                                                    <th>mic</th>
                                                    <th>staple</th>
                                                    <th>strength</th>
                                                    <th>uniform</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($summary as $row)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $row->sort }}</td>
                                                        <td>{{ $row->class }}</td>
                                                        <td>{{ $row->count }}</td>
                                                        <td>{{ $row->total_amount }}</td>
                                                        <td>{{ round($row->mic, 2) }}</td>
                                                        <td>{{ round($row->staple, 2) }}</td>
                                                        <td>{{ round($row->strength, 2) }}</td>
                                                        <td>{{ round($row->uniform, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        @if($clampData->isEmpty())
                                            <div class="alert alert-warning text-center">
                                                Ushbu dalolatnoma uchun HVI ma'lumotlari topilmadi
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table id="examples1" class="table table-striped table-bordered"
                                                       style="margin-top:20px;">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Gin_id</th>
                                                        <th>Gin_bale</th>
                                                        <th>Lot_number</th>
                                                        <th>Og'irlik</th>
                                                        <th>Selection</th>
                                                        <th>date_class</th>
                                                        <th>Time_class</th>
                                                        <th>classer_id</th>
                                                        <th>Klassiyor</th>
                                                        <th>croptype</th>
                                                        <th>grade</th>
                                                        <th>sort</th>
                                                        <th>class</th>
                                                        <th>staple</th>
                                                        <th>mic</th>
                                                        <th>leaf</th>
                                                        <th>strength</th>
                                                        <th>color_gr</th>
                                                        <th>color_rd</th>
                                                        <th>color_b</th>
                                                        <th>trash</th>
                                                        <th>uniform</th>
                                                        <th>fiblength</th>
                                                        <th>elongation</th>
                                                        <th>sfi</th>
                                                        <th>temperatur</th>
                                                        <th>humidity</th>
                                                        <th>hvi_num</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($clampData as $result)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $result->gin_id }}</td>
                                                            <td>{{ $result->gin_bale }}</td>
                                                            <td>{{ $result->lot_number }}</td>
                                                            <td>{{ $result->weight }}</td>
                                                            <td>{{ $result->selection }}</td>
                                                            <td>{{ $result->date_class }}</td>
                                                            <td>{{ $result->time_class }}</td>
                                                            <td>{{ $result->classer_id }}</td>
                                                            <td>{{ optional($result->klassiyor)->name }}</td>
                                                            <td>{{ $result->croptype }}</td>
                                                            <td>{{ $result->grade }}</td>
                                                            <td>{{ $result->sort }}</td>
                                                            <td>{{ $result->class }}</td>
                                                            <td>{{ $result->staple }}</td>
                                                            <td>{{ $result->mic }}</td>
                                                            <td>{{ $result->leaf }}</td>
                                                            <td>{{ $result->strength }}</td>
                                                            <td>{{ $result->color_gr }}</td>
                                                            <td>{{ $result->color_rd }}</td>
                                                            <td>{{ $result->color_b }}</td>
                                                            <td>{{ $result->trash }}</td>
                                                            <td>{{ $result->uniform }}</td>
                                                            <td>{{ $result->fiblength }}</td>
                                                            <td>{{ $result->elongation }}</td>
                                                            <td>{{ $result->sfi }}</td>
                                                            <td>{{ $result->temperatur }}</td>
                                                            <td>{{ $result->humidity }}</td>
                                                            <td>{{ $result->hvi_num }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Top Scroll Button -->
                                <button id="top-scroll-btn" onclick="scrollToTop()">Yuqoriga</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...';
            setTimeout(function () {
                button.disabled = false;
                button.innerText = 'HVI ma\'lumotlarini yuklash';
            }, 100000);
        }

        // JavaScript function to scroll to the top
        function scrollToTop() {
            document.body.scrollTop = 0;  // For Safari
            document.documentElement.scrollTop = 0;  // For Chrome, Firefox, IE, and Opera
        }

        // Show/hide the Top Scroll button based on the scroll position
        window.onscroll = function () {
            showTopScrollButton();
        };

        function showTopScrollButton() {
            var topScrollBtn = document.getElementById("top-scroll-btn");
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                topScrollBtn.style.display = "block";
            } else {
                topScrollBtn.style.display = "none";
            }
        }
    </script>
@endsection
