<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if (in_array($user->role, [\App\Models\User::ROLE_CITY_CHIGIT, \App\Models\User::ROLE_STATE_CHIGIT_BOSHLIQ, \App\Models\User::ROLE_STATE_CHIGI_XODIM])) {
            return redirect('/sifat-sertificates/list');
        }
        $branch_crop = session('crop', 1);

        $app_type_selector = $request->input('app_type_selector');
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');
        $year = session('year', 2024);

        $dalolatnomaQuery = Dalolatnoma::with('test_program');

        // Adjust sumAmountQuery with joins to filter by city indirectly
        $sumAmountQuery = AktAmount::join('dalolatnoma', 'akt_amount.dalolatnoma_id', '=', 'dalolatnoma.id')
            ->join('test_programs', 'dalolatnoma.test_program_id', '=', 'test_programs.id')
            ->join('applications', 'test_programs.app_id', '=', 'applications.id')
            ->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
            ->join('tbl_cities', 'organization_companies.city_id', '=', 'tbl_cities.id');

        // Adjust sumFinalResultQuery with similar joins to filter by city
        $sumFinalResultQuery = FinalResult::join('dalolatnoma', 'final_results.dalolatnoma_id', '=', 'dalolatnoma.id')
            ->join('test_programs', 'dalolatnoma.test_program_id', '=', 'test_programs.id')
            ->join('applications', 'test_programs.app_id', '=', 'applications.id')
            ->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
            ->join('tbl_cities', 'organization_companies.city_id', '=', 'tbl_cities.id');

        $appStatesQuery = Region::select('tbl_states.id', 'tbl_states.name', DB::raw('COUNT(applications.id) as application_count'))
            ->leftJoin('tbl_cities', 'tbl_states.id', '=', 'tbl_cities.state_id')
            ->leftJoin('organization_companies', 'tbl_cities.id', '=', 'organization_companies.city_id')
            ->join('applications', 'organization_companies.id', '=', 'applications.organization_id')
            ->join('test_programs', 'applications.id', '=', 'test_programs.app_id')
            ->join('dalolatnoma', 'test_programs.id', '=', 'dalolatnoma.test_program_id')
            ->leftJoin('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
            ->where('crop_data.year', $year);

        if ($from && $till) {
            [$fromTime, $tillTime] = $this->formatDateRange($from, $till);
            $dalolatnomaQuery->whereBetween('date', [$fromTime, $tillTime]);
            $sumAmountQuery->whereBetween('dalolatnoma.date', [$fromTime, $tillTime]);
            $sumFinalResultQuery->whereBetween('dalolatnoma.date', [$fromTime, $tillTime]);
            $appStatesQuery->whereBetween('dalolatnoma.date', [$fromTime, $tillTime]);
        }

        if ($city) {
            $this->applyCityFilter($dalolatnomaQuery, $city);

            // Apply city filter using joins
            $sumAmountQuery->where('tbl_cities.state_id', $city);
            $sumFinalResultQuery->where('tbl_cities.state_id', $city);
            $appStatesQuery->where('tbl_cities.state_id', $city);
        }

        if ($crop) {
            $appStatesQuery->where('crop_data.name_id', $crop);
        }

        $applications_count = $branch_crop == 1 ? $dalolatnomaQuery->count() : Application::count();
        $state_count =  $dalolatnomaQuery->count();
        $count_amount = $sumAmountQuery->count('akt_amount.id');
        $sum_amount = $sumAmountQuery->selectRaw('SUM(akt_amount.amount - dalolatnoma.tara) as total')->value('total');
        $sum_final_result = $sumFinalResultQuery->selectRaw('SUM(final_results.amount - (final_results.count * dalolatnoma.tara)) as total')->value('total');
        $app_states = $appStatesQuery->groupBy('tbl_states.id', 'tbl_states.name')->orderByDesc('application_count')->get();

        $states = DB::table('tbl_states')->where('country_id', 234)->get();
        $crop_names = DB::table('crops_name')->get();

        return view('dashboard.dashboard', compact(
            'app_states', 'states', 'crop_names', 'applications_count',
            'from', 'till', 'city', 'crop', 'app_type_selector',
            'sum_amount', 'count_amount', 'sum_final_result', 'state_count'
        ));
    }


// Helper method for formatting date range
    private function formatDateRange($from, $till): array
    {
        return [
            join('-', array_reverse(explode('-', $from))),
            join('-', array_reverse(explode('-', $till)))
        ];
    }

// Helper method for applying city filter on Eloquent relationship queries
    private function applyCityFilter($query, $city)
    {
        $query->whereHas('test_program.application.organization.city', function ($subQuery) use ($city) {
            $subQuery->where('state_id', $city);
        });
    }

}
