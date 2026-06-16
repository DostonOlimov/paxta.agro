<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\Region;
use App\Models\User;
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

        // Redirect based on user roles
        $redirectRoles = [
            User::ROLE_CITY_CHIGIT,
            User::ROLE_STATE_CHIGIT_BOSHLIQ,
            User::ROLE_STATE_CHIGI_XODIM
        ];
        if (in_array($user->role, $redirectRoles)) {
            return redirect('/sifat-sertificates/list');
        }

        $year = getCurrentYear();
        $branchCrop = getApplicationType();

        // Filter parameters
        $filters = [
            'app_type' => $request->input('app_type_selector'),
            'city' => $request->input('city'),
            'crop' => $request->input('crop'),
            'from' => $request->input('from'),
            'till' => $request->input('till'),
        ];

        // Base queries
        $applicationQuery = Application::query()
            ->join('prepared_companies', 'applications.prepared_id', '=', 'prepared_companies.id')
            ->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id');

        $appStatesQuery = Region::select(
            'tbl_states.id',
            'tbl_states.name',
            DB::raw('COUNT(applications.id) as application_count')
        )
            ->leftJoin('prepared_companies', 'tbl_states.id', '=', 'prepared_companies.state_id')
            ->join('applications', 'prepared_companies.id', '=', 'applications.prepared_id')
            ->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id');

        // Apply joins for non-branch crop type
        if ($branchCrop != 2) {
            $applicationQuery->join('test_programs', 'applications.id', '=', 'test_programs.app_id')
                ->join('dalolatnoma', 'test_programs.id', '=', 'dalolatnoma.test_program_id');

            $appStatesQuery->join('test_programs', 'applications.id', '=', 'test_programs.app_id')
                ->join('dalolatnoma', 'test_programs.id', '=', 'dalolatnoma.test_program_id');
        }

        // Filter by year and branch crop type
        // (Application has a global scope that already filters $applicationQuery by year/app_type)
        $appStatesQuery->where('crop_data.year', $year)
            ->where('applications.app_type', $branchCrop);

        // Date filters
        if ($filters['from'] && $filters['till']) {
            [$fromTime, $tillTime] = $this->formatDateRange($filters['from'], $filters['till']);

            $applicationQuery->whereBetween(
                $branchCrop == 2 ? 'date' : 'dalolatnoma.date',
                [$fromTime, $tillTime]
            );
            $appStatesQuery->whereBetween('dalolatnoma.date', [$fromTime, $tillTime]);
        }

        // City filter
        if ($filters['city']) {
            $applicationQuery->where('prepared_companies.state_id', $filters['city']);
            $appStatesQuery->where('prepared_companies.state_id', $filters['city']);
        }

        // Crop filter
        if ($filters['crop']) {
            $applicationQuery->where('crop_data.name_id', $filters['crop']);
            $appStatesQuery->where('crop_data.name_id', $filters['crop']);
        }

        $appStates = $appStatesQuery
            ->groupBy('tbl_states.id', 'tbl_states.name')
            ->orderByDesc('application_count')
            ->get();

        // Application counts and calculations
        $counts = $applicationQuery->clone()->selectRaw(
            'COUNT(applications.id) as applications_count'
            . ($branchCrop != 2 ? ', SUM(dalolatnoma.toy_count) as toy_count' : '')
        )->first();
        $applicationsCount = (int) $counts->applications_count;
        $toyCount = $branchCrop == 2 ? 0 : (float) $counts->toy_count;

        if ($branchCrop == 1) {
            $totals = $applicationQuery->clone()
                ->join('final_results', 'dalolatnoma.id', '=', 'final_results.dalolatnoma_id')
                ->join('sertificates', 'final_results.id', '=', 'sertificates.final_result_id')
                ->selectRaw(
                    'COUNT(applications.id) as sertificates_count, '
                    . 'COUNT(DISTINCT applications.id) as finished_count, '
                    . 'SUM(final_results.amount - (final_results.count * dalolatnoma.tara)) as final_result_total'
                )->first();

            $sertificatesCount = (int) $totals->sertificates_count;
            $finishedApplicationsCount = (int) $totals->finished_count;
            $sumFinalResult = $totals->final_result_total;
        } else {
            $finishedApplicationsCount = (int) $applicationQuery->clone()
                ->join('sifat_sertificates', 'sifat_sertificates.app_id', '=', 'applications.id')
                ->count('sifat_sertificates.id');
            $sertificatesCount = $finishedApplicationsCount;
            $sumFinalResult = $applicationQuery->clone()->sum('crop_data.amount');
        }

        $sumAmount = $branchCrop == 2
            ? $sumFinalResult
            : $applicationQuery->clone()
                ->join('akt_amount', 'dalolatnoma.id', '=', 'akt_amount.dalolatnoma_id')
                ->selectRaw('SUM(akt_amount.amount - dalolatnoma.tara) as total')->value('total');

        $cropNames = getCropsNames();
        $states = getRegions();
        

        return view('dashboard.dashboard', compact(
            'appStates', 'states', 'cropNames', 'applicationsCount',
            'filters', 'sumAmount', 'toyCount', 'sumFinalResult',
            'finishedApplicationsCount', 'branchCrop', 'sertificatesCount'
        ));
    }

// Helper method for formatting date range
    private function formatDateRange(string $from, string $till): array
    {
        return [
            join('-', array_reverse(explode('-', $from))),
            join('-', array_reverse(explode('-', $till)))
        ];
    }

}
