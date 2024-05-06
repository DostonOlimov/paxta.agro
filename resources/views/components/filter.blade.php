<div class="row">
    <div class="col-sm-4">
        <select class="w-100 form-control state_of_country custom-select " name="city" id="city">
            @if (count($states))
                <option value="">{{ trans('message.Respublika bo\'yicha') }}</option>
            @endif
            @if (!empty($states))
                @foreach ($states as $state)
                    <option value="{{ $state->id }}" @if ($city && $city == $state->id) selected="selected" @endif>
                        {{ trans('message.' . $state->name) }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="col-sm-4">
        <select class="w-100 form-control state_of_country custom-select" name="crop" id="crop">
            @if (count($crop_names))
                <option value="">{{ trans('message.Barchasi') }}</option>
            @endif
            @if (!empty($crop_names))
                @foreach ($crop_names as $state)
                    <option value="{{ $state->id }}" @if ($crop && $crop == $state->id) selected="selected" @endif>
                        {{ $state->name }} </option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div id="list-date-filter">
    <div class="show-date btn btn-default filter-button" style="background-color: #3160EE;color:white">
        <b>{{ trans('message.Vaqt bo\'yicha filtrlash') }}</b> <i
            class="fa {{ $from && $till ? 'fa-angle-left' : 'fa-angle-right' }}"></i></div>
    <div class="date {{ $from && $till ? 'open' : '' }}">
        <form class="input-filter">
            <input class="form-control fc-datepicker from input-filter" name="from" placeholder="dd-mm-yyyy"
                autocomplete="off" required="required"
                @if (!empty($from)) value="{{ $from }}" @endif />{{ trans('message.dan') }}
            <input class="form-control fc-datepicker till input-filter" name="till" placeholder="dd-mm-yyyy"
                autocomplete="off" required="required"
                @if (!empty($till)) value="{{ $till }}" @endif /> {{ trans('message.gacha') }}
            @if ($from && $till)
                <button type="button" class="btn btn-primary filter-button"
                    id="cancel-date-filter">{{ trans('message.Filtrni bekor qilish') }}
                </button>
            @else
                <button type='submit' class="btn btn-primary  filter-button">{{ trans('message.filterlash') }}
                </button>
            @endif
        </form>
    </div>
</div>
<div class="row">
    <div class="col-8">
        <div class="float-right-buttons">
            {{--            <div class="print-table-button btn btn-primary float-right-button" --}}
            {{--                 table='example-1'><i class='fa fa-print'></i> {{trans("app.Chop etish")}} --}}
            {{--            </div> --}}
        </div>
    </div>
    <div class="col-4">
        <form class="d-flex">
            <input type="text" name="s" class="search-input form-control"
                placeholder="{{ trans('message.Qidirish') }}" value="{{ isset($_GET['s']) ? $_GET['s'] : '' }}">
            <button type='submit' class="btn btn-primary"><i class="fa fa-search"></i>
            </button>
        </form>
    </div>
</div>
