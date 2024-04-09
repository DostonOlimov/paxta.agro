@extends('layouts.app')
@section('styles')
    <style>
        #my_table th {
            padding: 5px 5px ;!important;
            font-weight: bold;
            font-size: 16px;
        }
        #my_table td {
            padding: 5px 5px ;!important;
            font-size: 16px;
        }

        .container-layout {
            padding: 4px 5px 4px 1px;
            max-width: 212px;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            display: flex;
            align-items: center;
            column-gap: 6px;
            margin: 0 auto;
        }

        .layout-left {
            width: 50%;
            height: 70px;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding-right: 8px;
            border-right: 2px solid #969696;
        }

        .layout-right {
            border-bottom: 2px solid #3f51b5;
            border-top: 2px solid #3f51b5;
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(5, 1fr);
            grid-column-gap: 0px;
            grid-row-gap: 0px;
        }
        .layout-right-top{
           text-align: right;
        }
        .layout-right-bottom{
            text-align: right;
        }

        .layout-right > div {
            display: grid;
            place-items: center;
        }
        .div1 {
            grid-area: 1 / 2 / 6 / 3;
        }

        .div2 {
            grid-area: 1 / 1 / 2 / 2;
        }
        .div3 {
            grid-area: 1 / 3 / 2 / 4;
        }
        .div4 {
            grid-area: 2 / 1 / 3 / 2;
        }
        .div5 {
            grid-area: 2 / 3 / 3 / 4;
        }
        .div6 {
            grid-area: 3 / 1 / 4 / 2;
        }
        .div7 {
            grid-area: 3 / 3 / 4 / 4;
        }
        .div8 {
            grid-area: 4 / 1 / 5 / 2;
        }
        .div9 {
            grid-area: 4 / 3 / 5 / 4;
        }
        .div10 {
            grid-area: 5 / 1 / 6 / 2;
        }
        .div11 {
            grid-area: 5 / 3 / 6 / 4;
        }

        .layout__arrow {
            width: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .layout__arrow svg {
            width: 15px;
            height: 50px;
        }

        .layout__arrow-up {
            margin-top: -45px;
        }

        .layout__arrow-down {
            margin-bottom: -55px;
            transform: rotate(180deg);
        }

        .my_svg {
            position: absolute;
            z-index: 2;
            top: 1px;
            width: 8px;
            height: 15px;
            border-radius: 25px;
            background-color: #3f51b5;
        }

    </style>
@endsection
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp Muvofiqlik bo'yicha xulosa
                </li>
            </ol>
        </div>
        @if(session('message'))
            <div class="row massage">
                <div class="col-md-12 col-sm-12">
                    <div class="alert alert-success text-center">
                        <input id="checkbox-10" type="checkbox" checked="">
                        <label for="checkbox-10 colo_success">  {{session('message')}} </label>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li>
                                        <a href="{!! url('/measurement_mistake/search')!!}">
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-container">
                                            <div id="invoice-cheque" class="py-4 col-12" style=" font-family: Times New Roman;">
                                                <h3 class="text-right">Ilova F1 PSK2-07-2021</h3>
                                                <h2 class="text-center fw-bold">Muvofiqlik bo'yicha xulosalar № {{$result->id}} &nbsp; {{$date}}</h2>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h3 class="text-left">Buyurtmachi korxona {{ optional($result->dalolatnoma)->test_program->application->organization->name }}</h3>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h3 class="text-right">Partiya raqami {{ optional($result->dalolatnoma)->party }}</h3>
                                                    </div>
                                                </div>
                                            <div class="table-wrapper">
                                                <table id="my_table" class="table table-bordered nowrap" >
                                                <thead>
                                                <tr>
                                                    <th>Sifat ko'rsatkich nomi</th>
                                                    <th>O'rtacha qiymatlari</th>
                                                    <th>Upac</th>
                                                    <th>Normativ hujjat nomi</th>
                                                    <th>Me'yoriy chegaralari</th>
                                                    <th>Qaror qabul qilish chegarasi</th>
                                                    <th>Muvofiqligi</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Tashqi ko'rinishi va rangi</td>
                                                        <td>2</td>
                                                        <td>-</td>
                                                        <td>O'zDSt 604 3-jadval</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                @foreach($result->dalolatnoma->result as $f_result)
                                                    <tr>
                                                        <td>Tarkibidagi nuqsonlar va aralashmalar %</td>
                                                        <td>{{optional(\App\Models\CropsGeneration::where('kod','=',$f_result->class)->first())->name}}</td>
                                                        <td>-</td>
                                                        <td>O'zDSt 604 п.5.3</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                @endforeach
                                                    <tr>
                                                        @php
                                                            $length = round($result->dalolatnoma->laboratory_result->fiblength / 100,2);
                                                            $m_length = round($result->fiblength,3);
                                                        @endphp
                                                        <td>Yuqori o'rtacha uzunlik,(dyuym)</td>
                                                        <td>{{$length}}</td>
                                                        <td>{{$m_length}}</td>
                                                        <td>O'zDSt 604, 2-jadval</td>
                                                        <td>1,11 dan 1,17 gacha</td>
                                                        <td>
                                                            <div class="container-layout">
                                                                <div class="layout-left">
                                                                    <div class="layout__child-element-left">W = U</div>
                                                                    <div class="layout__child-element-left">O'rtacha</div>
                                                                </div>
                                                                <div class="layout-right-main">
                                                                <div class="layout-right-top">1.17</div>
                                                                <div class="layout-right">

                                                                    <div class="div1">
                                                                        <div class="layout__arrow">
                                                                            <svg
                                                                                class="layout__arrow-up"
                                                                                viewBox="0 0 48 60"
                                                                                version="1.0"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                            >
                                                                                <g fill="#3F51B5">
                                                                                    <path d="m24 4 11.7 14H12.3z" />
                                                                                    <path d="M20 15h8v200h-8z" />
                                                                                </g>
                                                                            </svg>
                                                                            <svg
                                                                                class="layout__arrow-down"
                                                                                viewBox="0 0 48 48"
                                                                                version="1.0"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                            >
                                                                                <g fill="#3F51B5">
                                                                                    <path d="m24 4 11.7 14H12.3z" />
                                                                                    <path d="M20 15h8v200h-8z" />
                                                                                </g>
                                                                            </svg>
{{--                                                                            <div class="my_svg"></div>--}}
                                                                        </div>
                                                                    </div>
                                                                    <div class="div2">{{round($length + $m_length,3)}}</div>
                                                                    <div class="div3">{{round($length + $m_length,3)}}</div>

                                                                    <div class="div6">{{$length}}</div>
                                                                    <div class="div7">{{$length}}</div>

                                                                    <div class="div10">{{round($length - $m_length,3)}}</div>
                                                                    <div class="div11">{{round($length - $m_length,3)}}</div>
                                                                </div>
                                                                <div class="layout-right-bottom">1.13</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                            $mic = round($result->dalolatnoma->laboratory_result->mic,1);
                                                            $m_mic= round($result->mic,2);
                                                        @endphp
                                                        <td>Mikroneer</td>
                                                        <td>{{$mic}}</td>
                                                        <td>{{$m_mic}}</td>
                                                        <td>O'zDSt б04 п.5.2.2</td>
                                                        <td>3,5 ± 4,9</td>
                                                        <td>
                                                            <div class="container-layout">
                                                                <div class="layout-left">
                                                                    <div class="layout__child-element-left">W = U</div>
                                                                    <div class="layout__child-element-left">O'rtacha</div>
                                                                </div>
                                                                <div class="layout-right-main">
                                                                    <div class="layout-right-top">4.9</div>
                                                                    <div class="layout-right">

                                                                        <div class="div1">
                                                                            <div class="layout__arrow">
                                                                                <svg
                                                                                    class="layout__arrow-up"
                                                                                    viewBox="0 0 48 60"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg
                                                                                    class="layout__arrow-down"
                                                                                    viewBox="0 0 48 48"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>

{{--                                                                                <div class="my_svg"></div>--}}
                                                                            </div>
                                                                        </div>
                                                                        <div class="div2">{{round($mic + $m_mic,2)}}</div>
                                                                        <div class="div3">{{round($mic + $m_mic,2)}}</div>

                                                                        <div class="div6">{{round($mic, 1)}}</div>
                                                                        <div class="div7">{{round($mic, 1)}}</div>

                                                                        <div class="div10">{{round($mic - $m_mic,2)}}</div>
                                                                        <div class="div11">{{round($mic - $m_mic,2)}}</div>
                                                                    </div>
                                                                    <div class="layout-right-bottom">3.5</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                            $uniform = round($result->dalolatnoma->laboratory_result->uniform,1);
                                                            $m_uniform= round($result->uniform,1);
                                                        @endphp
                                                        <td>Uzunlik bo'ylab bir xillik indeksi,%</td>
                                                        <td>{{$uniform}}</td>
                                                        <td>{{$m_uniform}}</td>
                                                        <td>O‘zDSt 604 Tahrir 1, 5-jadval</td>
                                                        <td>77 ± 86,0 va undan yuqori</td>
                                                        <td>
                                                            <div class="container-layout">
                                                                <div class="layout-left">
                                                                    <div class="layout__child-element-left">W = U</div>
                                                                    <div class="layout__child-element-left">O'rtacha</div>
                                                                </div>
                                                                <div class="layout-right-main">
                                                                    <div class="layout-right-top">86</div>
                                                                    <div class="layout-right">

                                                                        <div class="div1">
                                                                            <div class="layout__arrow">
                                                                                <svg
                                                                                    class="layout__arrow-up"
                                                                                    viewBox="0 0 48 60"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg
                                                                                    class="layout__arrow-down"
                                                                                    viewBox="0 0 48 48"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>

{{--                                                                                <div class="my_svg"></div>--}}
                                                                            </div>
                                                                        </div>
                                                                        <div class="div2">{{round($uniform + $m_uniform,2)}}</div>
                                                                        <div class="div3">{{round($uniform + $m_uniform,2)}}</div>

                                                                        <div class="div6">{{$uniform}}</div>
                                                                        <div class="div7">{{$uniform}}</div>

                                                                        <div class="div10">{{round($uniform - $m_uniform,2)}}</div>
                                                                        <div class="div11">{{round($uniform - $m_uniform,2)}}</div>
                                                                    </div>
                                                                    <div class="layout-right-bottom">77</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                            $strength = round($result->dalolatnoma->laboratory_result->strength,1);
                                                            $m_strength = round($result->strength,1);
                                                        @endphp
                                                        <td>Kuch gf/tex</td>
                                                        <td>{{$strength}}</td>
                                                        <td>{{$m_strength}}</td>
                                                        <td>O‘zDSt 604 Tahrir 1, 4-jadval</td>
                                                        <td>23 ± 33 va undan yuqori</td>
                                                        <td>
                                                            <div class="container-layout">
                                                                <div class="layout-left">
                                                                    <div class="layout__child-element-left">W = U</div>
                                                                    <div class="layout__child-element-left">O'rtacha</div>
                                                                </div>
                                                                <div class="layout-right-main">
                                                                    <div class="layout-right-top">33</div>
                                                                    <div class="layout-right">

                                                                        <div class="div1">
                                                                            <div class="layout__arrow">
                                                                                <svg
                                                                                    class="layout__arrow-up"
                                                                                    viewBox="0 0 48 60"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg
                                                                                    class="layout__arrow-down"
                                                                                    viewBox="0 0 48 48"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>
{{--                                                                                <div class="my_svg"></div>--}}
                                                                            </div>
                                                                        </div>
                                                                        <div class="div2">{{round($strength + $m_strength,2)}}</div>
                                                                        <div class="div3">{{round($strength + $m_strength,2)}}</div>

                                                                        <div class="div6">{{$strength}}</div>
                                                                        <div class="div7">{{$strength}}</div>

                                                                        <div class="div10">{{round($strength - $m_strength,2)}}</div>
                                                                        <div class="div11">{{round($strength - $m_strength,2)}}</div>
                                                                    </div>
                                                                    <div class="layout-right-bottom">23</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Namlikning massa nisbati, %</td>
                                                        <td>2</td>
                                                        <td>-</td>
                                                        <td>O'zDSt 604 п.5.7</td>
                                                        <td>5,0 ± 8,5 %</td>
                                                        <td>
                                                            <div class="container-layout">
                                                                <div class="layout-left">
                                                                    <div class="layout__child-element-left">W = U</div>
                                                                    <div class="layout__child-element-left">O'rtacha</div>
                                                                </div>
                                                                <div class="layout-right-main">
                                                                    <div class="layout-right-top">4.9</div>
                                                                    <div class="layout-right">

                                                                        <div class="div1">
                                                                            <div class="layout__arrow">
                                                                                <svg
                                                                                    class="layout__arrow-up"
                                                                                    viewBox="0 0 48 60"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg
                                                                                    class="layout__arrow-down"
                                                                                    viewBox="0 0 48 48"
                                                                                    version="1.0"
                                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                                >
                                                                                    <g fill="#3F51B5">
                                                                                        <path d="m24 4 11.7 14H12.3z" />
                                                                                        <path d="M20 15h8v200h-8z" />
                                                                                    </g>
                                                                                </svg>
{{--                                                                                <div class="my_svg"></div>--}}
                                                                            </div>
                                                                        </div>
                                                                        <div class="div2">{{round(($result->dalolatnoma->laboratory_result->mic + $result->mic),2)}}</div>
                                                                        <div class="div3">{{round(($result->dalolatnoma->laboratory_result->mic + $result->mic),2)}}</div>

                                                                        <div class="div6">{{round($result->dalolatnoma->laboratory_result->mic ,2)}}</div>
                                                                        <div class="div7">{{round($result->dalolatnoma->laboratory_result->mic ,2)}}</div>

                                                                        <div class="div10">{{round(($result->dalolatnoma->laboratory_result->mic - $result->mic),2)}}</div>
                                                                        <div class="div11">{{round(($result->dalolatnoma->laboratory_result->mic - $result->mic),2)}}</div>
                                                                    </div>
                                                                    <div class="layout-right-bottom">3.5</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>muvofiq</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="py-3">
                                            <a href="{{url()->previous()}}" class="btn btn-warning"><i class="fa fa-arrow-left"></i>{{trans('app.Orqaga')}}</a>
                                            <button class="btn btn-primary" id="print-invoice-btn"><i class="fa fa-print"></i> Chop etish</button>
                                        </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            function printCheque() {
                $('#invoice-cheque').print({
                    NoPrintSelector: '.no-print',
                    title: '',
                })
            }
            $('#print-invoice-btn').click(function (ev) {
                printCheque()
            })
        });
    </script>
@endsection
