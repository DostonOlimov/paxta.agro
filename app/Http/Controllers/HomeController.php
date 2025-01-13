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

        $branchCrop = session('crop', 1);
        $year = session('year', 2024);

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
            $appStatesQuery->where('crop_data.name_id', $filters['crop']);
        }

        $appStates = $appStatesQuery
            ->groupBy('tbl_states.id', 'tbl_states.name')
            ->orderByDesc('application_count')
            ->get();

        // Application counts and calculations
        $applicationsCount = $applicationQuery->count('applications.id');
        if ($branchCrop == 1) {
            $applicationQuery2 = $applicationQuery->clone()
                ->join('final_results', 'dalolatnoma.id', '=', 'final_results.dalolatnoma_id')
                ->join('sertificates', 'final_results.id', '=', 'sertificates.final_result_id');

            $sertificatesCount = $applicationQuery2->count('applications.id');

            $finishedApplicationsCount = $applicationQuery2->distinct('applications.id')->count('applications.id');
            $sumFinalResult = $applicationQuery2->selectRaw('SUM(final_results.amount - (final_results.count * dalolatnoma.tara)) as total')->value('total');
        } else {
            $applicationQuery2 = $applicationQuery->clone()
                ->join('sifat_sertificates', 'applications.id', '=', 'sifat_sertificates.app_id');

            $finishedApplicationsCount = $applicationQuery2->count('sifat_sertificates.id');
            $sertificatesCount = $finishedApplicationsCount;
            $sumFinalResult = $applicationQuery2->sum('crop_data.amount');
        }

        $toyCount = $branchCrop == 2 ? 0 : $applicationQuery->sum('dalolatnoma.toy_count');
        $sumAmount = $branchCrop == 2
            ? $applicationQuery->sum('crop_data.amount')
            : $applicationQuery->join('akt_amount', 'dalolatnoma.id', '=', 'akt_amount.dalolatnoma_id')
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
