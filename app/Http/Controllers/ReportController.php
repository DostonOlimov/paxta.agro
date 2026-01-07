<?php

namespace App\Http\Controllers;


use App\Jobs\ExportReportJob;
use App\Models\Application;
use App\Models\Area;
use App\Models\ExportRequest;
use App\Models\FinalResult;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CropsSelection;
use App\Models\OrganizationCompanies;
use App\Models\PreparedCompanies;

//use NunoMaduro\Collision\Adapters\Phpunit\State;

class ReportController extends Controller
{
    public function excel_export(Request $request)
    {
        try {
            // Create an export request record first
            $exportRequest = ExportRequest::create([
                'user_id' => Auth::id(),
                'filename' => 'hisobot_' . time() . '.xlsx',
                'status' => 'pending',
                'filters' => $request->all(),
            ]);

            // Dispatch the job to handle the export
            // Pass the request parameters instead of the full data set to avoid memory issues
            ExportReportJob::dispatch($exportRequest, $request->all());

            // Return a response indicating the export is in progress
            return response()->json(['message' => 'Export started successfully. You will be notified when it is ready.', 'status' => 'success']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Export failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'filters' => $request->all(),
            ]);

            return response()->json(['message' => 'Export failed: ' . $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function download_export($filename)
    {
        $userId = Auth::id();

        // Find the export request for this user and filename
        $exportRequest = ExportRequest::where('user_id', $userId)
            ->where('filename', $filename)
            ->first();

        if (!$exportRequest || $exportRequest->status !== 'completed') {
            return response()->json(['error' => 'File not found or not ready'], 404);
        }

        if (Storage::exists($exportRequest->file_path)) {
            return Storage::download($exportRequest->file_path, $filename);
        }

        return response()->json(['error' => 'File not found'], 404);
    }

    public function export_history(Request $request)
    {
        $userId = Auth::id();

        $exportRequests = ExportRequest::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Add download URL to each export request
        $exportRequests->getCollection()->transform(function ($exportRequest) {
            $exportRequest->download_url = $exportRequest->status === 'completed'
                ? route('excel.download', ['filename' => $exportRequest->filename])
                : null;
            return $exportRequest;
        });

        return response()->json(['data' => $exportRequests]);
    }

    public function excel_prepared(Request $request)
    {
        $user = Auth::user();
        $s = $request->input('s') ?? null;
        $data = $this->getReport($request);
        if (empty($city) && $user->branch_id == \App\Models\User::BRANCH_MAIN) {
            $user_city = 4012;
            $data = $data->whereHas('dalolatnoma.test_program.application.organization.city', function ($query) use ($user_city) {
                $query->where('state_id', $user_city);
            });
        }
        if ($s) {
            $data = $data->whereHas('dalolatnoma.test_program.application.prepared', function ($query) use ($s) {
                $query->where('name', 'like', '%' . $s . '%');
            });
        }
        $data = $data->get()
            ->groupBy(['dalolatnoma.test_program.application.prepared.name', 'dalolatnoma.test_program.application.prepared.kod']);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PreparedExport($data), 'hisobot.xlsx');
    }
    public function export_company(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city') ?? null;
        $crop = $request->input('crop') ?? null;
        $from = $request->input('from') ?? null;
        $till = $request->input('till') ?? null;
        $s = $request->input('s') ?? null;

        $companiesQuery = DB::table('dalolatnoma AS d')
            ->join('akt_amount AS akt', 'd.id', '=', 'akt.dalolatnoma_id')
            ->join('test_programs AS tp', 'd.test_program_id', '=', 'tp.id')
            ->join('applications AS app', 'tp.app_id', '=', 'app.id')
            ->join('organization_companies AS oc', 'app.organization_id', '=', 'oc.id')
            ->join('tbl_cities AS city', 'oc.city_id', '=', 'city.id')
            ->join('tbl_states AS state', 'city.state_id', '=', 'state.id')
            ->join('prepared_companies AS pc', 'app.prepared_id', '=', 'pc.id')
            ->select('oc.id', 'pc.kod', 'oc.name', DB::raw('count(akt.shtrix_kod) as kip'), DB::raw('sum(akt.amount) as netto'))
            ->groupBy('oc.id', 'pc.kod', 'oc.name');

        if ($user->branch_id == \App\Models\User::BRANCH_STATE) {
            $user_city = $user->state_id;
            $companiesQuery = $companiesQuery->whereExists(function ($query) use ($user_city) {
                $query->select(DB::raw(1))
                    ->from('organization_cities')
                    ->whereColumn('organization_cities.id', 'app.organization_id')
                    ->where('organization_cities.state_id', $user_city);
            });
        }

        if ($from && $till) {
            $from = Carbon::createFromFormat('d-m-Y', $from)->format('Y-m-d');
            $till = Carbon::createFromFormat('d-m-Y', $till)->format('Y-m-d');

            $companiesQuery = $companiesQuery->whereDate('d.date', '>=', $from)
                ->whereDate('d.date', '<=', $till);
        }
        if ($s) {
            $companiesQuery = $companiesQuery->where('oc.name', 'like', '%' . $s . '%');
        }

        if ($city) {
            $companiesQuery = $companiesQuery->where('state.id', $city);
        }

        $companies = $companiesQuery->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CompanyExport($companies), 'hisobot.xlsx');
    }

    public function report(Request $request)
    {
        $requestData = $request->only([
            'crop',
            'city',
            'region',
            'from',
            'till',
            'organization',
            'prepared',
            'number',
            'reester_number',
            'party_number',
            'sort',
            'class',
            'selection'
        ]);
        $city = $requestData['city'] ?? null;
        $region = $requestData['region'] ?? null;
        $crop = $requestData['crop'] ?? null;
        $selection = $requestData['selection'] ?? null;
        $from = $requestData['from'] ?? null;
        $till = $requestData['till'] ?? null;
        $organization = $requestData['organization'] ?? null;
        $prepared = $requestData['prepared'] ?? null;
        $number = $requestData['number'] ?? null;
        $resster_number = $requestData['resster_number'] ?? null;
        $party_number = $requestData['party_number'] ?? null;
        $sort = $requestData['sort'] ?? null;
        $class = $requestData['class'] ?? null;

        $results = $this->getReport($request);

        $totalSum = $results->sum('amount');
        $results = $results->latest('id')
            ->paginate(50)
            ->appends(['crop' => request()->input('crop')])
            ->appends(['till' => request()->input('till')])
            ->appends(['from' => request()->input('from')])
            ->appends(['city' => request()->input('city')])
            ->appends(['cities' => request()->input('cities')])
            ->appends(['organization' => request()->input('organization')])
            ->appends(['prepared' => request()->input('prepared')])
            ->appends(['states' => request()->input('states')])
            ->appends(['number' => request()->input('number')])
            ->appends(['selection' => request()->input('selection')])
            ->appends(['resster_number' => request()->input('resster_number')])
            ->appends(['party_number' => request()->input('party_number')])
            ->appends(['sort' => request()->input('sort')])
            ->appends(['class' => request()->input('class')])
            ->appends(['region' => request()->input('region')]);

        $states = DB::table('tbl_states')->where('country_id', 234)->get();
        $cities = $city ? DB::table('tbl_cities')->where('state_id', $city)->get() : '';
        if ($selection) {
            $selection = CropsSelection::find($selection);
        }
        if ($organization || $prepared) {
            $organization = OrganizationCompanies::find($organization);
            $prepared = PreparedCompanies::find($prepared);
            $city = $region = null;
        }


        return view('reports.full_report', compact('results', 'from', 'till', 'city', 'crop', 'totalSum', 'states', 'organization', 'prepared', 'cities', 'region', 'number', 'resster_number', 'party_number', 'sort', 'class', 'selection'));
    }

    public function company_report(Request $request)
    {
        $user = Auth::user();

        // Filters
        $city  = $request->input('city');
        $crop  = getApplicationType();      // original crop input is overridden, removed duplication
        $from  = $request->input('from');
        $till  = $request->input('till');
        $s     = $request->input('s');

        $year = getCurrentYear();

        // Base query
        $companiesQuery = DB::table('dalolatnoma AS d')
            ->join('akt_amount AS akt', 'd.id', '=', 'akt.dalolatnoma_id')
            ->join('test_programs AS tp', 'd.test_program_id', '=', 'tp.id')
            ->join('applications AS app', 'tp.app_id', '=', 'app.id')
            ->join('organization_companies AS oc', 'app.organization_id', '=', 'oc.id')
            ->join('tbl_cities AS city', 'oc.city_id', '=', 'city.id')
            ->join('tbl_states AS state', 'city.state_id', '=', 'state.id')
            ->join('prepared_companies AS pc', 'app.prepared_id', '=', 'pc.id')
            ->join('crop_data', 'app.crop_data_id', '=', 'crop_data.id')
            ->where('crop_data.year', $year)
            ->where('app.app_type', $crop)
            ->select(
                'oc.id',
                'pc.kod',
                'oc.name',
                DB::raw('COUNT(akt.shtrix_kod) AS kip'),
                DB::raw('SUM(akt.amount - d.tara) AS netto')
            )
            ->groupBy('oc.id', 'pc.kod', 'oc.name');

        // Filter: state restriction based on logged-in user
        if ($user->branch_id == \App\Models\User::BRANCH_STATE) {
            $companiesQuery->where('state.id', $user->state_id);
        }

        // Filter: date range
        if ($from && $till) {
            try {
                $fromDate = Carbon::createFromFormat('d-m-Y', $from)->format('Y-m-d');
                $tillDate = Carbon::createFromFormat('d-m-Y', $till)->format('Y-m-d');

                $companiesQuery->whereBetween('d.date', [$fromDate, $tillDate]);
            } catch (\Exception $e) {
                // Invalid date format, ignore the filter
            }
        }

        // Filter: company name
        if ($s) {
            $companiesQuery->where('oc.name', 'like', "%{$s}%");
        }

        // Filter: city selection
        if ($city) {
            $companiesQuery->where('state.id', $city);
        }

        // Clone query before pagination to calculate totals
        $companiesForTotals = $companiesQuery->get();

        $kipTotal = $companiesForTotals->sum('kip');

        $nettoTotal = $companiesForTotals->sum(function ($company) {
            return $company->netto ? round($company->netto / 1000, 4) : 0;
        });

        // Paginate
        $companies = $companiesQuery
            ->paginate(50)
            ->appends($request->except('page'));

        return view('reports.company_report', compact(
            'companies',
            'from',
            'till',
            'crop',
            'city',
            'kipTotal',
            'nettoTotal'
        ));
    }

    public function prepared_report(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city') ?? null;
        $crop = $request->input('crop') ?? null;
        $from = $request->input('from') ?? null;
        $till = $request->input('till') ?? null;
        $s = $request->input('s') ?? null;


        $prepareds = $this->getReport($request);
        if (empty($city) && $user->branch_id == \App\Models\User::BRANCH_MAIN) {
            $user_city = 4012;
            $prepareds = $prepareds->whereHas('dalolatnoma.test_program.application.organization.city', function ($query) use ($user_city) {
                $query->where('state_id', $user_city);
            });
        }
        $totalSum = $prepareds->sum('amount');
        if ($s) {
            $prepareds = $prepareds->whereHas('dalolatnoma.test_program.application.prepared', function ($query) use ($s) {
                $query->where('name', 'like', '%' . $s . '%');
            });
        }
        $prepareds = $prepareds->get()
            ->groupBy(['dalolatnoma.test_program.application.prepared.name', 'dalolatnoma.test_program.application.prepared.kod']);

        return view('reports.prepared_report', compact('prepareds', 'from', 'till', 'crop', 'city', 's', 'totalSum'));
    }


    public function myreport(Request $request)
    {
        $user = Auth::User();
        $app_type_selector = $request->input('app_type_selector');
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps = FinalResult::with('test_program');
        //        $apps = Application::with('organization')
        //            ->with('organization.city')
        //            ->with('organization.city.region')
        //            ->with('prepared')
        //            ->with('crops')
        //            ->with('crops.country')
        //            ->with('crops.name')
        //            ->with('crops.type')
        //            ->with('crops.generation')
        //            ->with('decision')
        //            ->with('tests')
        //            ->with('tests.result')
        //            ->with('tests.result.certificate')
        //            ->whereIn('status',[Application::STATUS_ACCEPTED,Application::STATUS_FINISHED]);

        if ($user->branch_id == \App\Models\User::BRANCH_STATE) {
            $user_city = $user->state_id;
            $apps = $apps->whereHas('organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps = $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }
        if ($city) {
            $apps = $apps->whereHas('organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        if (!is_null($app_type_selector)) {

            if ($app_type_selector == 3) {
                $apps = $apps->doesntHave('tests.result');
            } else {
                $apps = $apps->whereHas('tests.result', function ($query) use ($app_type_selector) {
                    $query->where('type', '=', $app_type_selector);
                });
            }
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->orWhere('app_number', $searchQuery);
                } else {
                    $query->whereHas('crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });
                }
            });
        });

        $apps = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('reports.report', compact('apps', 'from', 'till', 'city', 'crop'));
    }
    private function getReport($request)
    {
        // $year =  session('year') ?  session('year') : date('Y');

        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');
        $region = $request->input('region') ?? null;
        $organization = $request->input('organization') ?? null;
        $selection = $request->input('selection') ?? null;
        $prepared = $request->input('prepared') ?? null;
        $number =  $request->input('number') ?? null;
        $resster_number = $request->input('resster_number') ?? null;
        $party_number = $request->input('party_number') ?? null;
        $sort = $request->input('sort') ?? null;
        $class = $request->input('class') ?? null;


        if ($organization or $prepared) {
            $city = $region = null;
        }
        if ($organization || $prepared) {
            $city = $region = null;
        }

        $results = FinalResult::with([
            'generation',
            'certificate.attachment',
            'dalolatnoma.clamp_data',
            'dalolatnoma.selection',
            'dalolatnoma.akt_amount',
            'dalolatnoma.test_program.application.organization.city.region',
            'dalolatnoma.test_program.application.prepared',
            'dalolatnoma.test_program.application.crops.country',
            'dalolatnoma.test_program.application.crops.name',
            'dalolatnoma.test_program.application.crops.type',
            'dalolatnoma.test_program.application.decision',

        ]);


        if ($user->branch_id == \App\Models\User::BRANCH_STATE) {
            $user_city = $user->state_id;
            $results = $results->whereHas('dalolatnoma.test_program.application.organization.city', function ($query) use ($user_city) {
                $query->where('state_id', $user_city);
            });
        }
        if ($number) {
            $results = $results->whereHas('dalolatnoma', function ($query) use ($number) {
                $query->where('number', 'like', '%' . $number . '%');
            });
        }
        if ($resster_number) {
            $results = $results->whereHas('certificate', function ($query) use ($resster_number) {
                $query->where('reestr_number', 'like', '%' . $resster_number . '%');
            });
        }
        if ($selection) {

            $results = $results->whereHas('dalolatnoma.selection', function ($query) use ($selection) {
                $query->where('id', $selection);
            });
        }
        if ($party_number) {
            $results = $results->whereHas('dalolatnoma.test_program.application.crops', function ($query) use ($party_number) {
                $query->where('party_number', 'like', '%' . $party_number . '%');
            });
        }
        if ($sort) {
            $results = $results->where('sort', $sort);
        }
        if ($class) {
            $results = $results->where('class', $class);
        }

        if ($from && $till) {
            $from = Carbon::createFromFormat('d-m-Y', $from)->format('Y-m-d');
            $till = Carbon::createFromFormat('d-m-Y', $till)->format('Y-m-d');

            $results = $results->whereHas('dalolatnoma', function ($query) use ($from, $till) {
                $query->whereDate('date', '>=', $from)
                    ->whereDate('date', '<=', $till);
            });
        }

        if ($city) {
            $results = $results->whereHas('dalolatnoma.test_program.application.organization.city', function ($query) use ($city) {
                $query->where('state_id', $city);
            });
        }
        if ($region) {
            $area = Area::find($region);
            if ($city and $area->state_id == $city) {
                $results = $results->whereHas('dalolatnoma.test_program.application.organization', function ($query) use ($region) {
                    $query->where('city_id', '=', $region);
                });
            }
        }

        if ($organization) {
            $results = $results->whereHas('dalolatnoma.test_program.application.organization', function ($query) use ($organization) {
                $query->where('id', '=', $organization);
            });
        }

        if ($prepared) {
            $results = $results->whereHas('dalolatnoma.test_program.application.prepared', function ($query) use ($prepared) {
                $query->where('id', '=', $prepared);
            });
        }

        return $results;
    }
}
