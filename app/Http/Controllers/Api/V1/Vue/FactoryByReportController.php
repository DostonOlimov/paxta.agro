<?php

namespace App\Http\Controllers\Api\V1\Vue;

use App\Filters\V1\ApplicationFilter;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\V1\Vue\StateByReportCollection;
use App\Http\Resources\V1\Vue\StateByReportResource;
use App\Models\PreparedCompanies;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class FactoryByReportController extends Controller
{

    public function getReportByFactory(Request $request)
    {
        try {
            $crop = session('crop', 1);

            // Determine the data retrieval method based on the crop type
            $data = $crop == 1
                ? $this->getDataByMuvofiqlik($request)
                : $this->getDataBySifatSertificates($request);

            return $this->successResponse(
                new StateByReportCollection($data),
                'Data retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve the report: ' . $e->getMessage(),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function getDataBySifatSertificates(Request $request)
    {
        $query = $this->buildBaseQuery($request);

        return $query->addSelect([
            DB::raw('COUNT(CASE WHEN sifat_sertificates.id IS NOT NULL THEN applications.id END) as certificates_count'),
            DB::raw('SUM(CASE WHEN sifat_sertificates.id IS NOT NULL THEN crop_data.amount END) as application_amount'),
            DB::raw('COUNT(CASE WHEN sifat_sertificates.id IS NOT NULL THEN applications.id END) as certified_application_count')
        ])
            ->leftJoin('sifat_sertificates', 'applications.id', '=', 'sifat_sertificates.app_id')
            ->groupBy('prepared_companies.id', 'prepared_companies.name')
            ->orderByDesc('application_count')
            ->get();
    }

    private function getDataByMuvofiqlik(Request $request)
    {
        $query = $this->buildBaseQuery($request);

        return $query->addSelect([
            DB::raw('COUNT(DISTINCT CASE WHEN sertificates.id IS NOT NULL THEN sertificates.id END) as certificates_count'),
            DB::raw('SUM(CASE WHEN sertificates.id IS NOT NULL THEN (akt_amount.amount - dalolatnoma.tara) END) as application_amount'),
            DB::raw('COUNT(DISTINCT CASE WHEN sertificates.id IS NOT NULL THEN applications.id END) as certified_application_count')
        ])
            ->join('test_programs', 'applications.id', '=', 'test_programs.app_id')
            ->join('dalolatnoma', 'test_programs.id', '=', 'dalolatnoma.test_program_id')
            ->join('akt_amount', 'dalolatnoma.id', '=', 'akt_amount.dalolatnoma_id')
            ->leftJoin('final_results', 'dalolatnoma.id', '=', 'final_results.dalolatnoma_id')
            ->leftJoin('sertificates', 'final_results.id', '=', 'sertificates.final_result_id')
            ->groupBy('prepared_companies.id', 'prepared_companies.name')
            ->orderByDesc('application_count')
            ->get();
    }

    /**
     * Build base query for both types of data.
     */
    private function buildBaseQuery(Request $request)
    {
        $year = getCurrentYear();
        $branchCrop = getApplicationType();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $stateId = $request->input('stateId');

        $query = PreparedCompanies::select(
            'prepared_companies.id as id',
            'prepared_companies.name as name',
            DB::raw('COUNT(DISTINCT(applications.id)) as application_count')
        )
            ->join('applications', 'prepared_companies.id', '=', 'applications.prepared_id')
            ->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
            ->where('crop_data.year', $year)
            ->where('applications.app_type', $branchCrop)
            ->where('prepared_companies.state_id',$stateId);

        // Apply date filters
        if ($start_date) {
            $query->where('applications.date', '>=', $start_date);
        }
        if ($end_date) {
            $query->where('applications.date', '<=', $end_date);
        }

        return $query;
    }


}

