@extends('layouts.app')
@section('styles')
    <style>
        th {
            background-color: #2381c5 !important;
            color: white !important;
            font-weight: bold !important;
            white-space: nowrap; !important;
            text-align: center;
            font-size: 1rem !important;
        }

        table .form-control {
            font-size: 0.9rem !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #eaf2ee;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        .filter-button {
            margin-left: 0;
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
            background-color: #0E46A3  !important;
        }


        .table-responsive table {
            transform: rotate(180deg);
            direction: initial;
        }

        .table-responsive nav .pagination {
            padding-top: 13px;
            direction: initial;
            transform: rotate(180deg);
        }
    </style>
@endsection
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\Application::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp{{ trans('app.Barcha arizalar bo\'yicha umumiy ro\'yxat') }}
                    </li>
                </ol>
            </div>
            <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till" />
            <!--filter component -->

            <div class="row">
                <div class="col-sm-3 pt-2" style=" margin-top: -46px; margin-bottom: 13px;">
                    <button class="btn btn-success" style="color: white" id="export-excel-btn">
                        <i class="fa fa-file-excel-o"
                           style="margin-right: 6px; color: white;"></i>{{ trans('app.Excel fayl') }}</button>
                    <div id="export-status" style="margin-top: 10px; display: none;">
                        <span class="text-info">Eksport jarayonida...</span>
                    </div>
                    
                    <!-- Export History Section -->
                </div>
                 <div id="export-history" style="margin-top: 15px;">
                        <h5>So'nggi eksportlar</h5>
                        <div id="export-history-list">
                            <!-- Export tarixi bu yerda yuklanadi -->
                        </div>
                    </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered " style="margin-top:20px;">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">{{trans('app.Ariza sanasi')}}</th>
                                        <th rowspan="2">{{trans('app.Dalolatnoma raqami')}}</th>
                                        <th rowspan="2">{{trans('app.Sertifikat reestr raqami')}}</th>
                                        <th rowspan="2">{{trans('app.Na\'muna olingan viloyat')}}</th>
                                        <th rowspan="2">{{trans('app.Na\'muna olingan shahar yoki tuman')}}</th>
                                        <th rowspan="2">{{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}}</th>
                                        <th rowspan="2">{{trans('app.Tayorlangan shaxobcha yoki sexning nomi')}}</th>
                                        <th rowspan="2">{{trans('app.Name')}}</th>
                                        <th rowspan="2">{{trans('message.Seleksiya nomi')}}</th>
                                        <th rowspan="2">{{trans('app.Toʼda (partiya) raqami')}}</th>
                                        <th rowspan="2">{{trans('app.Hosil yili')}}</th>
                                        <th rowspan="2">{{trans("app.To'dadagi toylar soni (dona)")}}</th>
                                        <th rowspan="2">{{trans("app.amount")}}</th>
                                        <th rowspan="2">{{trans("app.Jami og'irlik(kg)")}}</th>
                                        <th rowspan="2">{{trans("app.Sof Og'irlik(kg)")}}</th>
                                        <th colspan="8" style="text-align: center">{{trans("app.Sifat nazorati natijalari")}}</th>
                                        <th rowspan="2">{{trans('app.Qaror fayllari')}}</th>
                                        <th rowspan="2">{{trans('app.Sinov bayonnoma fayllari')}}</th>
                                        <th rowspan="2">{{trans('app.Sertifikat fayllari')}}</th>
                                    </tr>
                                    <tr>
                                        <th>{{trans("app.Tip")}}</th>
                                        <th>{{trans("app.Sort")}}</th>
                                        <th>{{trans("app.Sinf")}}</th>
                                        <th>{{trans("app.Shtaple uzunligi")}}</th>
                                        <th>{{trans("app.Mikroneyr")}}</th>
                                        <th>{{trans("app.Solishtirma uzunlik kuchi")}}</th>
                                        <th>{{trans("app.Uzunligi bo'yicha bir xillik ko'rsatkichi,%")}}</th>
                                        <th>{{trans("app.Namlik ko'rsatkichi,%")}}</th>
                                    </tr>

                                    </thead>
                                    <tbody>

                                    <tr style="background-color: #90aec6 !important;">
                                        <td> </td>
                                        <td> </td>
                                        <td>
                                            <form class="d-flex">
                                                <input type="text" name="number" class="search-input form-control"
                                                       value="{{ isset($_GET['number']) ? $_GET['number'] : '' }}" >
                                            </form>
                                        </td>

                                        <td>
                                            <form class="d-flex">
                                                <input type="text" name="resster_number" class="search-input form-control"
                                                       value="{{ isset($_GET['resster_number']) ? $_GET['resster_number'] : '' }}">

                                            </form>
                                        </td>
                                        <td>
                                            <select class="w-100 form-control state_of_country custom-select "
                                                    name="city" id="city1" url="{!! url('/getcityfromstate') !!}">
                                                @if (count($states))
                                                    <option value="">{{ trans('app.Viloyat tanlang') }}</option>
                                                @endif
                                                @if (!empty($states))
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}"
                                                                @if ($city && $city == $state->id) selected="selected" @endif>
                                                            {{ __('message.' . $state->name) }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control w-100 city_of_state custom-select" name="region"
                                                    id="region">
                                                <option value="">{{ trans('app.Tumanni tanlang') }}</option>
                                                @if (!empty($cities))
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}"
                                                                @if (($region && $region == $city->id) || count($cities) == 1) selected="selected" @endif>
                                                            {{ $city->name }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <select id="organization" class="form-control owner_search" name="organization">
                                                @if (!empty($organization))
                                                    <option selected value="{{ $organization->id }}">
                                                        {{ $organization->name }}</option>
                                                @endif
                                            </select>
                                            @if ($organization)
                                                <i class="fa fa-trash" style="color:red"
                                                   onclick="changeDisplay('organization')"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <select id="prepared" class="form-control owner_search2" name="prepared">
                                                @if (!empty($prepared))
                                                    <option selected value="{{ $prepared->id }}">{{ $prepared->name }}
                                                    </option>
                                                @endif
                                            </select>
                                            @if ($prepared)
                                                <i class="fa fa-trash" style="color:red"
                                                   onclick="changeDisplay('prepared')"></i>
                                            @endif
                                        </td>
                                        <td></td>
                                        <td>
                                            <select id="selection" class="form-control seletions" name="selection">
                                                @if (!empty($selection))
                                                    <option selected value="{{ $selection->id }}">
                                                        {{ $selection->name }}</option>
                                                @endif
                                            </select>
                                            @if ($selection)
                                                <i class="fa fa-trash" style="color:red"
                                                    onclick="changeDisplay('selection')"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <form class="d-flex">
                                                <input type="text" name="party_number" class="search-input form-control"
                                                       value="{{ isset($_GET['party_number']) ? $_GET['party_number'] : '' }}" >
                                            </form>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <select class="form-control w-100 city_of_state custom-select" name="sort"
                                                    id="sort">
                                                <option value="">{{ trans('app.Sortni tanlang') }}</option>
                                                <option value="1" {{($sort==1)? "selected":''}} >1</option>
                                                <option value="2" {{($sort==2)? "selected":''}} >2</option>
                                                <option value="3" {{($sort==3)? "selected":''}} >3</option>
                                                <option value="4" {{($sort==4)? "selected":''}} >4</option>
                                                <option value="5" {{($sort==5)? "selected":''}} >5</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control w-100 city_of_state custom-select" name="class"
                                                    id="class">
                                                <option value="">{{ trans('app.Sinfni tanlang') }}</option>
                                                <option value="1" {{($class==1)? "selected":''}}>OLIY</option>
                                                <option value="2" {{($class==2)? "selected":''}}>YAXSHI</option>
                                                <option value="3" {{($class==3)? "selected":''}}>O'RTA</option>
                                                <option value="4" {{($class==4)? "selected":''}}>ODDIY</option>
                                                <option value="5" {{($class==5)? "selected":''}}>IFLOS</option>
                                            </select>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @if (isset($results))
                                        @foreach($results as $result)
                                            <tr>
                                                @if (isset($result->dalolatnoma->test_program->application))
                                                    <td>{{$offset + $loop->iteration}}</td>
                                                    <td><a href="{!! url('/application/view/'.optional($result->dalolatnoma->test_program->application)->id) !!}">{{ optional($result->dalolatnoma->test_program->application)->date }}</a></td>
                                                    <td>{{ optional($result->dalolatnoma)->number }}</td>
                                                    <td>{{ optional($result->certificate)->reestr_number }}</td>
                                                    <td>{{ __('message.' . optional($result->dalolatnoma->test_program->application->organization)->city->region->name) }}</td>
                                                    <td>{{ optional($result->dalolatnoma->test_program->application->organization)->city->name }}</td>
                                                    <td><a href="{!! url('/organization/view/'.$result->dalolatnoma->test_program->application->organization_id) !!}">{{ optional($result->dalolatnoma->test_program->application->organization)->name }}</a></td>
                                                    <td>{{ optional($result->dalolatnoma->test_program->application->prepared)->name }}</td>
                                                    <td>{{ optional($result->dalolatnoma->test_program->application->crops->name)->name }}</td>
                                                    <td>{{ optional($result->dalolatnoma->selection)->name }}</td>
                                                    <td>{{ optional($result->dalolatnoma->test_program->application->crops)->party_number }}</td>
                                                    <td>{{ optional($result->dalolatnoma->test_program->application->crops)->year }}</td>

                                                    <td> {{ $result->count}}</td>
                                                    <td>{{ optional($result->dalolatnoma->akt_amount)->sum('amount') }}</td>
                                                    <td> {{ (optional($result)->amount)? $result->amount." kg":''}}</td>
                                                    <td> {{ ($result->amount != null)?$result->amount - $result->count * optional($result->dalolatnoma)->tara : ''}}</td>
                                                    <td> 4</td>
                                                    <td> {{ $result->sort}}</td>
                                                    <td> {{ optional($result->generation)->name}}</td>
                                                    <td> {{ round($result->staple)}}</td>
                                                    <td> {{ round($result->mic,1)}}</td>
                                                    <td> {{ round($result->strength,1)}}</td>
                                                    <td> {{ round($result->uniform,1)}}</td>
                                                    <td> {{ round(($result->humidity),2)}}</td>

                                                    <td>@if($result->dalolatnoma->test_program->application->decision)
                                                            <a href="{!! url('/decision/view/'.optional($result->dalolatnoma->test_program->application->decision)->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Qaror fayli')}}</button></a>
                                                        @endif
                                                    </td> <td>@if($result->dalolatnoma->test_program->application->tests)
                                                            <a href="{!! url('/tests/view/'.$result->dalolatnoma->test_program->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sinov dasturi fayli')}}</button></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($result->certificate)
                                                            <a href="{{route('attachment.download', ['id' => $result->certificate->attachment->id])}}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sertifikat fayli')}}</button></a>
                                                        @endif
                                                    </td>
                                                @endif

                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                {{ $results->links() }}
                            </div>

                            <h4
                                style="position: sticky; bottom: 0; padding: 1%; color: #0052cc; width: 100%; display: flex; justify-content: space-between; background-color: white">
                                <span>{{($totalSum)? trans("app.Jami og'irlik(kg)").': '.number_format($totalSum, 2, ',', ' '):''}}</span>
                            </h4>
                        </div>
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
    <!-- /page content -->
    <!-- /page content -->
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <script>
        function changeDisplay(name) {
            //organization companies change
            var currentUrl = window.location.href;
            var url = new URL(currentUrl);

            // Set the new query parameter
            url.searchParams.set(name, '');

            // Modify the URL and trigger an AJAX request
            var newUrl = url.toString();
            window.history.pushState({
                path: newUrl
            }, '', newUrl);

            $.ajax({
                url: newUrl,
                method: "GET",
                success: function(response) {
                    window.location.reload(true);
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#city1').change(function () {

                console.log('jsf');
                var selectedCity = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('city', selectedCity);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({ path: newUrl }, '', newUrl);

                $.ajax({
                    url:  newUrl,
                    method: "GET",
                    success: function (response) {
                        window.location.reload(true);
                    }
                });
            });

            $('#region').change(function() {
                var selectedRegion = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('region', selectedRegion);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                $.ajax({
                    url: newUrl,
                    method: "GET",
                    success: function(response) {
                        window.location.reload(true);
                    }
                });
            });
        });
        // add url for filter
        $(document).ready(function() {
            //organization companies change
            $('#organization').change(function() {
                var selectedRegion = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('organization', selectedRegion);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                $.ajax({
                    url: newUrl,
                    method: "GET",
                    success: function(response) {
                        window.location.reload(true);
                    }
                });
            });
            //selection companies change
            $('#selection').change(function() {
                var selectedRegion = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('selection', selectedRegion);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                $.ajax({
                    url: newUrl,
                    method: "GET",
                    success: function(response) {
                        window.location.reload(true);
                    }
                });
            });
            //prepared companies change
            $('#prepared').change(function() {
                var selectedRegion = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('prepared', selectedRegion);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                $.ajax({
                    url: newUrl,
                    method: "GET",
                    success: function(response) {
                        window.location.reload(true);
                    }
                });
            });
        });
        $(document).ready(function() {
            //organization companies change
            $('#class').change(function() {
                var selectedRegion = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('class', selectedRegion);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                $.ajax({
                    url: newUrl,
                    method: "GET",
                    success: function(response) {
                        window.location.reload(true);
                    }
                });
            });
            //prepared companies change
            $('#sort').change(function() {
                var selectedRegion = $(this).val();

                var currentUrl = window.location.href;
                var url = new URL(currentUrl);

                // Set the new query parameter
                url.searchParams.set('sort', selectedRegion);

                // Modify the URL and trigger an AJAX request
                var newUrl = url.toString();
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                $.ajax({
                    url: newUrl,
                    method: "GET",
                    success: function(response) {
                        window.location.reload(true);
                    }
                });
            });
        });
    </script>
    {{--    appllication add --}}
    <script>
        $(document).ready(function() {
            $('select.owner_search').select2({
                ajax: {
                    url: '/organization/search_by_name',
                    delay: 300,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        data = data.map((name, index) => {
                            return {
                                id: name.id,
                                text: capitalize(name.name + (name.name ? ' - STiR:' + name
                                    .inn : ''))
                            }
                        });
                        return {
                            results: data
                        }
                    }
                },
                language: {
                    inputTooShort: function() {
                        return '{{ trans('app.Korxona (nomi), STIR ini kiritib izlang') }}';
                    },
                    searching: function() {
                        return '{{ trans('app.Izlanmoqda...') }}';
                    },
                    noResults: function() {
                        return '{{ trans('app.Natija topilmadi') }}'
                    },
                    errorLoading: function() {
                        return '{{ trans('app.Natija topilmadi') }}'
                    }
                },
                placeholder: '{{ trans('app.Korxona nomini kiriting') }}',
                minimumInputLength: 2
            })
            $('select.seletions').select2({
                ajax: {
                    url: '/crops_selection/search_by_name',
                    delay: 300,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        data = data.map((name, index) => {
                            return {
                                id: name.id,
                                text: capitalize(name.name + (name.name ? ' - kod:' + name
                                    .kod : ''))
                            }
                        });
                        return {
                            results: data
                        }
                    }
                },
                language: {
                    inputTooShort: function() {
                        return '{{ trans('app.Seleksiya (nomi), kod ini kiritib izlang') }}';
                    },
                    searching: function() {
                        return '{{ trans('app.Izlanmoqda...') }}';
                    },
                    noResults: function() {
                        return '{{ trans('app.Natija topilmadi') }}'
                    },
                    errorLoading: function() {
                        return '{{ trans('app.Natija topilmadi') }}'
                    }
                },
                placeholder: '{{ trans('app.Seleksiyani nomini kiriting') }}',
                minimumInputLength: 2
            })
            $('select.owner_search2').select2({
                ajax: {
                    url: '/prepared/search_by_name',
                    delay: 300,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        data = data.map((name, index) => {
                            return {
                                id: name.id,
                                text: capitalize(name.name)
                            }
                        });
                        return {
                            results: data
                        }
                    }
                },
                language: {
                    inputTooShort: function() {
                        return '{{ trans('app.Korxona nomini kiritib izlang') }}';
                    },
                    searching: function() {
                        return '{{ trans('app.Izlanmoqda...') }}';
                    },
                    noResults: function() {
                        return '{{ trans('app.Natija topilmadi') }}'
                    },
                    errorLoading: function() {
                        return '{{ trans('app.Natija topilmadi') }}'
                    }
                },
                placeholder: '{{ trans('app.Korxona nomini kiriting') }}',
                minimumInputLength: 2
            })

            function capitalize(text) {
                var words = text.split(' ');
                for (var i = 0; i < words.length; i++) {
                    if (words[i][0] == null) {
                        continue;
                    } else {
                        words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
                    }

                }
                return words.join(' ');
            }
        });
        
        // Handle Excel export button click
        $(document).on('click', '#export-excel-btn', function() {
            // Show loading status
            $('#export-status').show();
            
            // Get current URL parameters
            var currentUrl = window.location.href;
            var url = new URL(currentUrl);
            
            // Build query parameters object
            var params = {
                from: url.searchParams.get('from') || '',
                till: url.searchParams.get('till') || '',
                city: url.searchParams.get('city') || '',
                crop: url.searchParams.get('crop') || '',
                region: url.searchParams.get('region') || '',
                organization: url.searchParams.get('organization') || '',
                prepared: url.searchParams.get('prepared') || '',
                number: url.searchParams.get('number') || '',
                resster_number: url.searchParams.get('resster_number') || '',
                party_number: url.searchParams.get('party_number') || '',
                sort: url.searchParams.get('sort') || '',
                class: url.searchParams.get('class') || '',
                selection: url.searchParams.get('selection') || '',
            };
            
            // Make AJAX request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: '{{ route("excel.export") }}',
                method: 'GET',
                data: params,
                success: function(response) {
                    // Hide loading status
                    $('#export-status').hide();
                    
                    // Show success message
                    alert('Eksport boshlandi. Tayyor bo\'lganda sizga xabar beriladi. Bildirishnomalaringizni tekshiring.');
                    
                    // Reload export history to show the new request
                    loadExportHistory();
                    
                    // Optional: Implement download functionality when file is ready
                    // This would require polling or WebSocket implementation to check file availability
                },
                complete: function() {
                    // Hide loading status in any case
                    $('#export-status').hide();
                },
                error: function(xhr, status, error) {
                    // Hide loading status
                    $('#export-status').hide();
                    
                    // Show error message
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Noma\'lum xatolik';
                    alert('Eksport qilishda xatolik yuz berdi: ' + errorMessage);
                    console.error('Export error:', xhr, status, error);
                }
            });
        });
        
        // Load export history
        function loadExportHistory() {
            $.ajax({
                url: '{{ route("excel.history") }}',
                method: 'GET',
                success: function(response) {
                    var html = '<ul class="list-group">';
                    
                    if (response.data.data.length === 0) {
                        html += '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Hisobotlar tarixi topilmadi</div>';
                    } else {
                        html += '<div class="table-responsive"><table class="table table-bordered table-hover">';
                        html += '<thead class="thead-light"><tr><th>Fayl nomi</th><th>Holati</th><th>Yaratilgan</th><th>Amallar</th></tr></thead><tbody>';
                        
                        response.data.data.forEach(function(request) {
                            var statusClass = '';
                            var statusText = '';
                            
                            switch(request.status) {
                                case 'completed':
                                    statusClass = 'text-success';
                                    statusText = 'Tayyor';
                                    break;
                                case 'processing':
                                    statusClass = 'text-info';
                                    statusText = 'Jarayonda';
                                    break;
                                case 'failed':
                                    statusClass = 'text-danger';
                                    statusText = 'Bajarilmadi';
                                    break;
                                default:
                                    statusClass = 'text-warning';
                                    statusText = 'Kutilmoqda';
                            }
                            
                            html += '<tr>';
                            html += '<td><i class="fas fa-file-excel text-success"></i> ' + request.filename + '</td>';
                            html += '<td><span class="badge ' + statusClass + ' badge-pill">' + statusText + '</span></td>';
                            html += '<td><small class="text-muted">' + new Date(request.created_at).toLocaleString() + '</small></td>';
                            html += '<td>';
                            if (request.status === 'completed' && request.download_url) {
                                html += '<a href="' + request.download_url + '" class="btn btn-sm btn-success btn-block"><i class="fa fa-download"></i> Yuklab olish</a>';
                            } else {
                                html += '<span class="text-muted">Mavjud emas</span>';
                            }
                            html += '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                    }
                    
                    html += '</ul>';
                    
                    // Add pagination if available
                    if (response.data.links && response.data.links.length > 0) {
                        html += '<nav aria-label="Export history pagination">';
                        html += '<ul class="pagination pagination-sm">';
                        
                        response.data.links.forEach(function(link) {
                            if (link.url) {
                                html += '<li class="page-item ' + (link.active ? 'active' : '') + '">' +
                                        '<a class="page-link" href="javascript:void(0);" onclick="loadExportHistoryPage(\'' + link.url + '\')">' +
                                        link.label + '</a></li>';
                            } else {
                                html += '<li class="page-item disabled">' +
                                        '<span class="page-link">' + link.label + '</span></li>';
                            }
                        });
                        
                        html += '</ul>';
                        html += '</nav>';
                    }
                    
                    $('#export-history-list').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Eksport tarixini yuklashda xatolik:', xhr, status, error);
                    // Foydalanuvchiga xato to'g'risida bildirishnoma ko'rsatish mumkin
                    // alert('Eksport tarixini yuklashda xatolik: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Noma'lum xatolik'));
                }
            });
        }
        
        // Load export history on page load
        $(document).ready(function() {
            loadExportHistory();
            
            // Refresh history every 30 seconds
            setInterval(loadExportHistory, 30000);
        });
        
        // Function to load specific page of export history
        function loadExportHistoryPage(url) {
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    var html = '';
                    
                    if (response.data.data.length === 0) {
                        html += '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Eksport tarixi topilmadi</div>';
                    } else {
                        html += '<div class="table-responsive"><table class="table table-bordered table-hover">';
                        html += '<thead class="thead-light"><tr><th>Fayl nomi</th><th>Holati</th><th>Yaratilgan</th><th>Amallar</th></tr></thead><tbody>';
                        
                        response.data.data.forEach(function(request) {
                            var statusClass = '';
                            var statusText = '';
                            
                            switch(request.status) {
                                case 'completed':
                                    statusClass = 'text-success';
                                    statusText = 'Tayyor';
                                    break;
                                case 'processing':
                                    statusClass = 'text-info';
                                    statusText = 'Jarayonda';
                                    break;
                                case 'failed':
                                    statusClass = 'text-danger';
                                    statusText = 'Bajarilmadi';
                                    break;
                                default:
                                    statusClass = 'text-warning';
                                    statusText = 'Kutilmoqda';
                            }
                            
                            html += '<tr>';
                            html += '<td><i class="fas fa-file-excel text-success"></i> ' + request.filename + '</td>';
                            html += '<td><span class="badge ' + statusClass + ' badge-pill">' + statusText + '</span></td>';
                            html += '<td><small class="text-muted">' + new Date(request.created_at).toLocaleString() + '</small></td>';
                            html += '<td>';
                            if (request.status === 'completed' && request.download_url) {
                                html += '<a href="' + request.download_url + '" class="btn btn-sm btn-success btn-block"><i class="fas fa-download"></i> Yuklab olish</a>';
                            } else {
                                html += '<span class="text-muted">Mavjud emas</span>';
                            }
                            html += '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                    }
                    
                    // Add pagination
                    if (response.data.links && response.data.links.length > 0) {
                        html += '<nav aria-label="Export history pagination">';
                        html += '<ul class="pagination pagination-sm">';
                        
                        response.data.links.forEach(function(link) {
                            if (link.url) {
                                html += '<li class="page-item ' + (link.active ? 'active' : '') + '">' +
                                        '<a class="page-link" href="javascript:void(0);" onclick="loadExportHistoryPage(\'' + link.url + '\')">' +
                                        link.label + '</a></li>';
                            } else {
                                html += '<li class="page-item disabled">' +
                                        '<span class="page-link">' + link.label + '</span></li>';
                            }
                        });
                        
                        html += '</ul>';
                        html += '</nav>';
                    }
                    
                    $('#export-history-list').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Eksport tarixi sahifasini yuklashda xatolik:', xhr, status, error);
                    // Foydalanuvchiga xato to'g'risida bildirishnoma ko'rsatish mumkin
                    // alert('Eksport tarixi sahifasini yuklashda xatolik: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Noma'lum xatolik'));
                }
            });
        }
    </script>


@endsection
