@extends('layouts.app')
@section('styles')
    <style>
        th {
            background-color: #2381c5 !important;
            color: white !important;
            font-weight: bold !important;
            white-space: nowrap !important;
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

        /* The double rotation puts the horizontal scrollbar above the table instead of below
           it. Scoped to .report-scroll on purpose: on plain .table-responsive it also flips
           any other scrollable table on the page (the export history) upside down. */
        .report-scroll {
            transform: rotate(180deg);
            direction: rtl;
        }

        .report-scroll::-webkit-scrollbar {
            transform: rotate(180deg);
            height: 16px;
            background-color: #d4d4d4;
            border-radius: 25px !important;
        }

        .report-scroll::-webkit-scrollbar-thumb {
            cursor: pointer;
            background-color: #0E46A3 !important;
        }

        .report-scroll > table {
            transform: rotate(180deg);
            direction: initial;
        }

        .report-toolbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .report-total {
            position: sticky;
            bottom: 0;
            padding: 1%;
            color: #0052cc;
            width: 100%;
            display: flex;
            justify-content: space-between;
            background-color: white;
        }
    </style>
@endsection
@section('content')
    <!-- page content -->
    @can('viewAny', \App\Models\Application::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp;{{ trans('app.Barcha arizalar bo\'yicha umumiy ro\'yxat') }}
                    </li>
                </ol>
            </div>
            <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till" />
            <!--filter component -->

            <div class="report-toolbar">
                <div>
                    <button type="button" class="btn btn-success" style="color: white" id="export-excel-btn">
                        <i class="fa fa-file-excel-o" style="margin-right: 6px; color: white;"></i>{{ trans('app.Excel fayl') }}
                    </button>
                    <span id="export-status" class="text-info ml-2" style="display: none;">
                        {{ trans('app.Eksport jarayonida...') }}
                    </span>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="export-history-toggle">
                    <i class="fa fa-history"></i> {{ trans('app.So\'nggi eksportlar') }}
                </button>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Export History Section -->
                    <div id="export-history" class="card" style="display: none;">
                        <div class="card-body">
                            <h5>{{ trans('app.So\'nggi eksportlar') }}</h5>
                            <div id="export-history-list"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive report-scroll">
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

                                    {{-- Filter row: one cell per column, so every control sits under its own header --}}
                                    <tr id="report-filters" style="background-color: #90aec6 !important;">
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input type="text" name="number" data-filter="number"
                                                   class="search-input form-control"
                                                   value="{{ $number }}">
                                        </td>
                                        <td>
                                            <input type="text" name="resster_number" data-filter="resster_number"
                                                   class="search-input form-control"
                                                   value="{{ $resster_number }}">
                                        </td>
                                        <td>
                                            <select class="w-100 form-control custom-select" data-filter="city"
                                                    name="city" id="city1">
                                                <option value="">{{ trans('app.Viloyat tanlang') }}</option>
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                            @if ($city && $city == $state->id) selected="selected" @endif>
                                                        {{ __('message.' . $state->name) }} </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control w-100 custom-select" data-filter="region"
                                                    name="region" id="region">
                                                <option value="">{{ trans('app.Tumanni tanlang') }}</option>
                                                @foreach ($cities as $cityOption)
                                                    <option value="{{ $cityOption->id }}"
                                                            @if ($region && $region == $cityOption->id) selected="selected" @endif>
                                                        {{ $cityOption->name }} </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select id="organization" class="form-control owner_search"
                                                    data-filter="organization" name="organization">
                                                @if (!empty($organization))
                                                    <option selected value="{{ $organization->id }}">
                                                        {{ $organization->name }}</option>
                                                @endif
                                            </select>
                                            @if ($organization)
                                                <i class="fa fa-trash" style="color:red; cursor:pointer"
                                                   onclick="clearFilter('organization')"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <select id="prepared" class="form-control owner_search2"
                                                    data-filter="prepared" name="prepared">
                                                @if (!empty($prepared))
                                                    <option selected value="{{ $prepared->id }}">{{ $prepared->name }}
                                                    </option>
                                                @endif
                                            </select>
                                            @if ($prepared)
                                                <i class="fa fa-trash" style="color:red; cursor:pointer"
                                                   onclick="clearFilter('prepared')"></i>
                                            @endif
                                        </td>
                                        <td></td>
                                        <td>
                                            <select id="selection" class="form-control seletions"
                                                    data-filter="selection" name="selection">
                                                @if (!empty($selection))
                                                    <option selected value="{{ $selection->id }}">
                                                        {{ $selection->name }}</option>
                                                @endif
                                            </select>
                                            @if ($selection)
                                                <i class="fa fa-trash" style="color:red; cursor:pointer"
                                                   onclick="clearFilter('selection')"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" name="party_number" data-filter="party_number"
                                                   class="search-input form-control"
                                                   value="{{ $party_number }}">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <select class="form-control w-100 custom-select" data-filter="sort"
                                                    name="sort" id="sort">
                                                <option value="">{{ trans('app.Sortni tanlang') }}</option>
                                                @foreach ([1, 2, 3, 4, 5] as $sortOption)
                                                    <option value="{{ $sortOption }}"
                                                            {{ $sort == $sortOption ? 'selected' : '' }}>{{ $sortOption }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control w-100 custom-select" data-filter="class"
                                                    name="class" id="class">
                                                <option value="">{{ trans('app.Sinfni tanlang') }}</option>
                                                @foreach (['1' => 'OLIY', '2' => 'YAXSHI', '3' => "O'RTA", '4' => 'ODDIY', '5' => 'IFLOS'] as $classValue => $classLabel)
                                                    <option value="{{ $classValue }}"
                                                            {{ $class == $classValue ? 'selected' : '' }}>{{ $classLabel }}</option>
                                                @endforeach
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
                                    </tr>
                                    @php
                                        $offset = ($results->currentPage() - 1) * $results->perPage();
                                    @endphp
                                    @forelse($results as $result)
                                        @continue(!isset($result->dalolatnoma->test_program->application))
                                        @php
                                            $application = $result->dalolatnoma->test_program->application;
                                            $tara = optional($result->dalolatnoma)->tara ?? 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $offset + $loop->iteration }}</td>
                                            <td><a href="{!! url('/application/view/'.$application->id) !!}">{{ $application->date }}</a></td>
                                            <td>{{ optional($result->dalolatnoma)->number }}</td>
                                            <td>{{ optional($result->certificate)->reestr_number }}</td>
                                            <td>{{ ($regionName = data_get($application, 'organization.city.region.name')) ? __('message.' . $regionName) : '' }}</td>
                                            <td>{{ data_get($application, 'organization.city.name') }}</td>
                                            <td><a href="{!! url('/organization/view/'.$application->organization_id) !!}">{{ data_get($application, 'organization.name') }}</a></td>
                                            <td>{{ data_get($application, 'prepared.name') }}</td>
                                            <td>{{ data_get($application, 'crops.name.name') }}</td>
                                            <td>{{ optional($result->dalolatnoma->selection)->name }}</td>
                                            <td>{{ data_get($application, 'crops.party_number') }}</td>
                                            <td>{{ data_get($application, 'crops.year') }}</td>

                                            <td>{{ $result->count }}</td>
                                            <td>{{ optional($result->dalolatnoma)->akt_amount_sum_amount ?? 0 }}</td>
                                            <td>{{ $result->amount ? $result->amount . ' kg' : '' }}</td>
                                            <td>{{ $result->amount !== null ? $result->amount - $result->count * $tara : '' }}</td>
                                            <td>4</td>
                                            <td>{{ $result->sort }}</td>
                                            <td>{{ optional($result->generation)->name }}</td>
                                            <td>{{ $result->staple !== null ? round($result->staple) : '' }}</td>
                                            <td>{{ $result->mic !== null ? round($result->mic, 1) : '' }}</td>
                                            <td>{{ $result->strength !== null ? round($result->strength, 1) : '' }}</td>
                                            <td>{{ $result->uniform !== null ? round($result->uniform, 1) : '' }}</td>
                                            <td>{{ $result->humidity !== null ? round($result->humidity, 2) : '' }}</td>

                                            <td>
                                                @if($application->decision)
                                                    <a href="{!! url('/decision/view/'.$application->decision->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Qaror fayli')}}</button></a>
                                                @endif
                                            </td>
                                            <td>
                                                @if($application->tests)
                                                    <a href="{!! url('/tests/view/'.$result->dalolatnoma->test_program->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sinov dasturi fayli')}}</button></a>
                                                @endif
                                            </td>
                                            <td>
                                                @if($result->certificate && $result->certificate->attachment)
                                                    <a href="{{route('attachment.download', ['id' => $result->certificate->attachment->id])}}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sertifikat fayli')}}</button></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="27" class="text-center">{{ trans('app.Natija topilmadi') }}</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $results->links() }}

                            <h4 class="report-total">
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan
    <!-- /page content -->
@endsection

@section('scripts')
    <script>
        // ---------------------------------------------------------------------
        // Filtering: every control writes its value into the query string and
        // reloads, so filters combine instead of replacing each other.
        // ---------------------------------------------------------------------
        function applyFilter(name, value) {
            var url = new URL(window.location.href);

            if (value === null || value === '') {
                url.searchParams.delete(name);
            } else {
                url.searchParams.set(name, value);
            }

            // a changed filter gives a different result set, so start over at page 1
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        function clearFilter(name) {
            applyFilter(name, '');
        }

        $(document).ready(function () {
            // scoped to the in-table filter row; the filter component's selects handle themselves
            $(document).on('change', '#report-filters select[data-filter]', function () {
                applyFilter($(this).data('filter'), $(this).val());
            });

            $(document).on('keydown', '#report-filters input[data-filter]', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    applyFilter($(this).data('filter'), $.trim($(this).val()));
                }
            });

            // The date range and the search box are plain GET forms. Submitted as-is they
            // navigate to a URL built only from their own fields, dropping every other
            // active filter, so merge them into the current query string instead.
            $(document).on('submit', 'form', function (e) {
                var form = $(this);

                if ((form.attr('method') || 'get').toLowerCase() !== 'get') {
                    return; // leave POST forms (logout, ...) alone
                }

                e.preventDefault();

                var url = new URL(window.location.href);

                form.serializeArray().forEach(function (field) {
                    if (field.value === '') {
                        url.searchParams.delete(field.name);
                    } else {
                        url.searchParams.set(field.name, field.value);
                    }
                });

                url.searchParams.delete('page');
                window.location.href = url.toString();
            });
        });
    </script>
    {{-- select2 lookups for the organization / prepared / selection filters --}}
    <script>
        $(document).ready(function() {
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

            function remoteSelect(selector, url, mapText, options) {
                $(selector).select2({
                    ajax: {
                        url: url,
                        delay: 300,
                        dataType: 'json',
                        data: function(params) {
                            return {
                                search: params.term
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: capitalize(mapText(item))
                                    };
                                })
                            };
                        }
                    },
                    language: {
                        inputTooShort: function() {
                            return options.tooShort;
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
                    placeholder: options.placeholder,
                    minimumInputLength: 2
                });
            }

            remoteSelect('select.owner_search', '/organization/search_by_name', function(item) {
                return item.name + (item.name ? ' - STiR:' + item.inn : '');
            }, {
                tooShort: '{{ trans('app.Korxona (nomi), STIR ini kiritib izlang') }}',
                placeholder: '{{ trans('app.Korxona nomini kiriting') }}'
            });

            remoteSelect('select.seletions', '/crops_selection/search_by_name', function(item) {
                return item.name + (item.name ? ' - kod:' + item.kod : '');
            }, {
                tooShort: '{{ trans('app.Seleksiya (nomi), kod ini kiritib izlang') }}',
                placeholder: '{{ trans('app.Seleksiyani nomini kiriting') }}'
            });

            remoteSelect('select.owner_search2', '/prepared/search_by_name', function(item) {
                return item.name;
            }, {
                tooShort: '{{ trans('app.Korxona nomini kiritib izlang') }}',
                placeholder: '{{ trans('app.Korxona nomini kiriting') }}'
            });
        });
    </script>
    {{-- Excel export + export history --}}
    <script>
        var EXPORT_LABELS = {
            completed: { css: 'badge-success', text: '{{ trans('app.Tayyor') }}' },
            processing: { css: 'badge-info', text: '{{ trans('app.Jarayonda') }}' },
            failed: { css: 'badge-danger', text: '{{ trans('app.Bajarilmadi') }}' },
            pending: { css: 'badge-warning', text: '{{ trans('app.Kutilmoqda') }}' }
        };

        var exportHistoryTimer = null;

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : value).html();
        }

        function renderExportHistory(payload) {
            var rows = payload.data || [];
            var html = '';

            if (rows.length === 0) {
                $('#export-history-list').html(
                    '<div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ trans('app.Natija topilmadi') }}</div>'
                );
                return false;
            }

            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered table-hover">';
            html += '<thead class="thead-light"><tr>' +
                    '<th>{{ trans('app.Fayl nomi') }}</th>' +
                    '<th>{{ trans('app.Holati') }}</th>' +
                    '<th>{{ trans('app.Yaratilgan') }}</th>' +
                    '<th>{{ trans('app.Amallar') }}</th>' +
                    '</tr></thead><tbody>';

            var hasUnfinished = false;

            rows.forEach(function (request) {
                var status = EXPORT_LABELS[request.status] || EXPORT_LABELS.pending;

                if (request.status !== 'completed' && request.status !== 'failed') {
                    hasUnfinished = true;
                }

                html += '<tr>';
                html += '<td><i class="fa fa-file-excel-o text-success"></i> ' + escapeHtml(request.filename) + '</td>';
                html += '<td><span class="badge ' + status.css + ' badge-pill">' + status.text + '</span></td>';
                html += '<td><small class="text-muted">' + escapeHtml(new Date(request.created_at).toLocaleString()) + '</small></td>';
                html += '<td>';
                if (request.status === 'completed' && request.download_url) {
                    html += '<a href="' + escapeHtml(request.download_url) + '" class="btn btn-sm btn-success">' +
                            '<i class="fa fa-download"></i> {{ trans('app.Yuklab olish') }}</a>';
                } else {
                    html += '<span class="text-muted">&mdash;</span>';
                }
                html += '</td></tr>';
            });

            html += '</tbody></table></div>';

            if (payload.links && payload.links.length > 0) {
                html += '<nav><ul class="pagination pagination-sm">';
                payload.links.forEach(function (link) {
                    if (link.url) {
                        html += '<li class="page-item ' + (link.active ? 'active' : '') + '">' +
                                '<a class="page-link" href="javascript:void(0);" data-history-url="' + escapeHtml(link.url) + '">' +
                                link.label + '</a></li>';
                    } else {
                        html += '<li class="page-item disabled"><span class="page-link">' + link.label + '</span></li>';
                    }
                });
                html += '</ul></nav>';
            }

            $('#export-history-list').html(html);

            return hasUnfinished;
        }

        function loadExportHistory(url) {
            $.ajax({
                url: url || '{{ route("excel.history") }}',
                method: 'GET',
                success: function (response) {
                    var hasUnfinished = renderExportHistory(response.data);

                    // only keep polling while something is still being generated
                    clearTimeout(exportHistoryTimer);
                    if (hasUnfinished) {
                        exportHistoryTimer = setTimeout(function () {
                            loadExportHistory();
                        }, 10000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Eksport tarixini yuklashda xatolik:', xhr, status, error);
                }
            });
        }

        $(document).ready(function () {
            $('#export-history-toggle').on('click', function () {
                var panel = $('#export-history');
                panel.toggle();
                if (panel.is(':visible')) {
                    loadExportHistory();
                } else {
                    clearTimeout(exportHistoryTimer);
                }
            });

            $(document).on('click', '[data-history-url]', function () {
                loadExportHistory($(this).data('history-url'));
            });

            $('#export-excel-btn').on('click', function () {
                var button = $(this);

                button.prop('disabled', true);
                $('#export-status').show();

                // export exactly what the page is showing: the whole current filter set
                var params = new URL(window.location.href).searchParams;
                params.delete('page');

                $.ajax({
                    url: '{{ route("excel.export") }}',
                    method: 'GET',
                    data: params.toString(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        alert('{{ trans('app.Eksport boshlandi. Tayyor bo\'lganda sizga xabar beriladi.') }}');
                        $('#export-history').show();
                        loadExportHistory();
                    },
                    error: function (xhr, status, error) {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Noma\'lum xatolik';
                        alert('Eksport qilishda xatolik yuz berdi: ' + errorMessage);
                        console.error('Export error:', xhr, status, error);
                    },
                    complete: function () {
                        button.prop('disabled', false);
                        $('#export-status').hide();
                    }
                });
            });
        });
    </script>
@endsection
