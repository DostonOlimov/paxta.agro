@extends('layouts.app')
<link rel="stylesheet" href="{{ URL::asset('resources/assets/css/dashboard.css') }}"/>

@section('content')

    <div class="scores welcome">
        <div class="my-title">
            <h2>@php  echo date('d-m-Y ') @endphp</h2>
        </div>
        <div class="welcome-inside">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h2 class="font-weight-bold text-light">{{trans('message.Xush kelibsiz')}}!</h2>
                            <h4 class="font-weight-normal text-light mb-0">&nbsp {{ auth()->user()->name.' '.auth()->user()->lastname}} {{trans('message.tizimga muvaffaqiyatli kirdingiz.')}} </h4>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="scores" >
        <div class="my-title">
            <h2>{{trans('message.Kelib tushgan arizalar')}}</h2>
        </div>
        <div class="circle-persentages">
            <div class="circle_persentages__statistics">
                <div class="circle-persentages__circle">
                    <svg viewBox="0 0 4.5 4.5" xmlns="http://www.w3.org/2000/svg">
                        <!-- Lower circle -->
                        <circle
                            cx="2.25"
                            cy="2.25"
                            r="2"
                            fill="transparent"
                            stroke="#494a4e"
                            stroke-width="0.35"
                        />
                        <!-- Actual circle -->
                        <circle
                            cx="2.25"
                            cy="2.25"
                            r="2"
                            fill="transparent"
                            stroke="#4fe4ff"
                            stroke-width="0.35"
                            stroke-dasharray="13 100"
                            stroke-linecap="round"
                            transform="rotate(-90 2.25 2.25)"
                        />
                    </svg>
                    <div class="circle">
                        <div class="mask full">
                            <div class="fill"></div>
                        </div>

                        <div class="mask half">
                            <div class="fill"></div>
                        </div>

                        <div class="inside-circle">100%</div>
                    </div>
                </div>
                <div class="circle_persentages__statistics__texts">
                    <h3 class="lineUp">{{trans('message.Jami arizalar')}}</h3>
                    <p class="lineUp">{{$all_app_count}} {{trans('message.ta')}}</p>
                </div>
            </div>
            <div class="circle_persentages__statistics">
                <div class="circle-persentages__circle">
                    <svg viewBox="0 0 4.5 4.5" xmlns="http://www.w3.org/2000/svg">
                        <!-- Lower circle -->
                        <circle
                            cx="2.25"
                            cy="2.25"
                            r="2"
                            fill="transparent"
                            stroke="#353941"
                            stroke-width="0.40"
                        />
                        <!-- Actual circle -->
                        <circle
                            cx="2.25"
                            cy="2.25"
                            r="2"
                            fill="transparent"
                            stroke="#c50fb4"
                            stroke-width="0.40"
                            stroke-dasharray="11 100"
                            stroke-linecap="round"
                            transform="rotate(-90 2.25 2.25)"
                        />
                    </svg>
                    <div class="circle">
                        <div class="mask full">
                            <div class="fill"></div>
                        </div>

                        <div class="mask half">
                            <div class="fill"></div>
                        </div>

                        <div class="inside-circle">@php echo round(100 * ($all_app_count > 0 ? $local_app / $all_app_count : 0),1);@endphp%</div>
                    </div>
                </div>
                <div class="circle_persentages__statistics__texts">
                    <h3 class="lineUp">{{trans('message.Mahaliy arizalar')}}</h3>
                    <p class="lineUp">{{$local_app}} {{trans('message.ta')}}</p>
                </div>
            </div>
            <div class="circle_persentages__statistics">
                <div class="circle-persentages__circle">
                    <svg viewBox="0 0 4.5 4.5" xmlns="http://www.w3.org/2000/svg">
                        <!-- Lower circle -->
                        <circle
                            cx="2.25"
                            cy="2.25"
                            r="2"
                            fill="transparent"
                            stroke="#353941"
                            stroke-width="0.35"
                        />
                        <!-- Actual circle -->
                        <circle
                            cx="2.25"
                            cy="2.25"
                            r="2"
                            fill="transparent"
                            stroke="#009dd4"
                            stroke-width="0.35"
                            stroke-dasharray="1 100"
                            stroke-linecap="round"
                            transform="rotate(-90 2.25 2.25)"
                        />
                    </svg>
                    <div class="circle">
                        <div class="mask full">
                            <div class="fill"></div>
                        </div>

                        <div class="mask half">
                            <div class="fill"></div>
                        </div>

                        <div class="inside-circle">@php echo round(100 * ($all_app_count > 0 ? $global_app / $all_app_count : 0),1);@endphp%</div>
                    </div>
                </div>
                <div class="circle_persentages__statistics__texts">
                    <h3 class="lineUp">{{trans('message.Xorijiy arizalar:')}}</h3>
                    <p class="lineUp">{{$global_app}} {{trans('message.ta')}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="scores empty">
        <div class="my-title">
            <h2>{{trans('message.filterlash')}}</h2>
        </div>
        <div class="empty-inside">
            <div class="row">
                <div class="col-sm-4">
                    <select class="w-100 form-control state_of_country custom-select" name="app_type_selector" id="app_type_selector">

                        <option value="">{{trans('message.Barcha arizalar')}}</option>
                        <option value="3" @if( ( $app_type_selector && $app_type_selector == 3))  selected="selected" @endif>{{trans('message.Jarayon to\'liq yakunlanmagan arizalar')}} </option>
                        <option value="2" @if( ( $app_type_selector && $app_type_selector == 2))  selected="selected" @endif> {{trans('message.Sertifikat berilgan arizalar')}} </option>
                        <option value="0" @if( ( !is_null($app_type_selector) && $app_type_selector == 0))  selected="selected" @endif>{{trans('message.Nomuvofiqligi uchun sertifikat berilmagan arizalar')}}</option>
                        <option value="1" @if( ( $app_type_selector && $app_type_selector == 1))  selected="selected" @endif>{{trans('message.Boshqa sabablarga ko\'ra sertifikat berilmagan arizalar')}}</option>
                    </select>
                </div>
                <div class="col-sm-8">
                    <div id="list-date-filter" class="p-0 m-0 " >
                        <div class="show-date btn btn-default filter-button m-0 rounded-2" style="font-size: 15px;" >{{trans('message.Vaqt bo\'yicha filtrlash')}} <i
                                class="fa {{ ($from && $till) ? 'fa-angle-left':'fa-angle-right' }}"></i></div>
                        <div class="date {{($from && $till) ? 'open':''}}">
                            <form class="input-filter pr-0 mr-0">
                                <input class="form-control fc-datepicker from input-filter" name="from"
                                       placeholder="dd-mm-yyyy" autocomplete="off" required="required"
                                       @if(!empty($from))
                                       value="{{$from}}"
                                    @endif
                                /> <span style="color:white">{{trans('message.dan')}}</span>
                                <input class="form-control fc-datepicker till input-filter" name="till"
                                       placeholder="dd-mm-yyyy" autocomplete="off" required="required"
                                       @if(!empty($till))
                                       value="{{$till}}"
                                    @endif
                                /> <span style="color:white">{{trans('message.gacha')}}</span>
                                @if($from && $till)
                                    <button type="button" class="btn btn-primary filter-button"
                                            id="cancel-date-filter">{{trans('message.Filtrni bekor qilish')}}
                                    </button>
                                @else
                                    <button type='submit' class="btn btn-primary  filter-button">{{trans('message.filterlash')}}
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="scores workblocks">
        <div class="my-title row">
            <div class="col-sm-8">
                <h2>{{trans('message.Paxta tolasi kesimida ma\'lumot')}}</h2>
            </div>
            <div class="col-sm-4">
                <select class="w-100 form-control state_of_country custom-select " name="city" id="city">
                    @if(count($states))
                        <option value="">{{trans('message.Respublika bo\'yicha')}}</option>
                    @endif

                    @if(!empty($states))

                        @foreach($states as $state)

                            <option value="{{ $state->id }}" @if( ($city && $city == $state->id))  selected="selected" @endif

                            > {{$state->name}} </option>

                        @endforeach

                    @endif

                </select>
            </div>
        </div>
        <div class="workblocks__info">
            <div class="workblocks__info__row lineUp" style="animation-delay: 0.5s;">
                <div class="workblocks__info__row__color"></div>
                <div class="workblocks__info__row__date">
                    <a href="#" rel="noopener noreferrer">
                        T/r</a>
                </div>
                <div class="workblocks__info__row__theme">
                    <a href="#" rel="noopener noreferrer">{{trans('app.Name')}}</a>
                </div>
                <div class="workblocks__info__row__duration">
                    <a href="#" rel="noopener noreferrer"
                    >{{trans('message.Arizalar soni')}}</a
                    >
                </div>
                <div class="workblocks__info__row__score">
                    <a href="#" rel="noopener noreferrer">{{trans('message.Miqdori')}}</a>
                </div>
                <div class="workblocks__info__row__score">
                    <a href="#" rel="noopener noreferrer">{{trans('message.O\'lchov birligi')}}</a>
                </div>
            </div>
            @foreach($crops as $item)
            <div class="workblocks__info__row lineUp" style="animation-delay: 0.25s;">
                <div
                    class="workblocks__info__row__color workblocks__info__row__color__secondary"
                ></div>
                <div class="workblocks__info__row__date">
                    <a href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$city.'&crop='.$item->id.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer">{{ $loop->iteration}}</a>
                </div>
                <div class="workblocks__info__row__theme">
                    <a href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$city.'&crop='.$item->id.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer"
                    >{{$item->name}}</a
                    >
                </div>
                <div class="workblocks__info__row__duration">
                    <a href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$city.'&crop='.$item->id.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer">{{$item->count}} {{trans('message.ta')}}</a>
                </div>
                <div class="workblocks__info__row__score">
                    <a href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$city.'&crop='.$item->id.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer">{{round($sum_amount/1000,3)}}</a>
                </div>
                <div class="workblocks__info__row__score">
                    <a href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$city.'&crop='.$item->id.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer">@if($item->id == 20 or $item->id == 8) {{'dona'}} @else {{trans('message.tonna')}} @endif</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="scores workblocks">
        <div class="my-title row">
            <div class="col-sm-8">
                <h2>{{trans('message.Viloyatlar kesimida ma\'lumot')}}</h2>
            </div>
            <div class="col-sm-4">
                <select class="w-100 form-control state_of_country custom-select" name="crop" id="crop">
                    @if(count($crop_names))
                        <option value="">{{trans('message.Barchasi')}}</option>
                    @endif

                    @if(!empty($crop_names))

                        @foreach($crop_names as $state)

                            <option value="{{ $state->id }}" @if( ($crop && $crop == $state->id))  selected="selected" @endif> {{$state->name}} </option>

                        @endforeach

                    @endif

                </select>
            </div>
        </div>
        <div class="workblocks__info">
            @foreach($app_states as $app_state)
            <div
                class="workblocks__info__row lineUp"
                style="animation-delay: 0.25s"
            >
                <div class="workblocks__info__row__percentage">
                    <a  href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$app_state->id.'&crop='.$crop.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer">{{round(100*$app_state->application_count / $app_states->sum('application_count'))}}%</a>
                </div>
                <div class="workblocks__info__row__theme">
                    <a  href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$app_state->id.'&crop='.$crop.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer">{{$app_state->name}}</a>
                </div>
                <div class="progress-bar">
                    <div class="progress fill-{{$loop->iteration}}"></div>
                </div>
                <div class="workblocks__info__row__duration">
                    <a  href="{{ url('full-report?till='.$till.'&from='.$from.'&city='.$app_state->id.'&crop='.$crop.'&app_type_selector='.$app_type_selector) }}" target="_blank" rel="noopener noreferrer"
                    >{{$app_state->application_count}} {{trans('message.ta')}}</a
                    >
                </div>
            </div>
            @endforeach
        </div>
    </div>

@endsection
