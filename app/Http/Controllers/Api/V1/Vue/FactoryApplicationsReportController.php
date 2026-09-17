<?php

namespace App\Http\Controllers\Api\V1\Vue;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\CropsName;
use App\Models\PreparedCompanies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Application list of one factory (vue/factory-applications/{id}),
 * opened from the factory name in vue/factory-report.
 */
class FactoryApplicationsReportController extends Controller
{
    public function getApplicationsByFactory(Request $request)
    {
        try {
            $factory = PreparedCompanies::findOrFail($request->input('factoryId'));
            $crop = getApplicationType();

            $rows = $crop == CropsName::CROP_TYPE_1
                ? $this->getDataByMuvofiqlik($request)
                : $this->getDataBySifatSertificates($request, $factory);

            return $this->successResponse([
                'factory' => [
                    'id' => $factory->id,
                    'name' => $factory->name,
                    'kod' => $factory->kod,
                    'state_id' => $factory->state_id,
                ],
                'show_konditsion' => $crop == CropsName::CROP_TYPE_2,
                'applications' => $rows,
            ], 'Data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve the report: ' . $e->getMessage(),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function getDataBySifatSertificates(Request $request, PreparedCompanies $factory)
    {
        $isChigit = getApplicationType() == CropsName::CROP_TYPE_2;

        return $this->buildBaseQuery($request)
            ->leftJoin('sifat_sertificates', 'applications.id', '=', 'sifat_sertificates.app_id')
            ->addSelect([
                'crop_data.amount as amount',
                'sifat_sertificates.id as certificate_id',
                'sifat_sertificates.number as certificate_number',
                'sifat_sertificates.type as certificate_type',
                'sifat_sertificates.amount as konditsion_amount',
            ])
            ->get()
            ->map(function ($row) use ($factory, $isChigit) {
                // same number format as sifat_sertificate(2)/list.blade.php
                $number = $row->certificate_number
                    ? substr(10000000 + ($row->certificate_type == 1 ? 1000 * $factory->kod : 500000) + $row->certificate_number, 2)
                    : null;

                return $this->formatRow($row, $number, (float) $row->amount, $isChigit ? round($row->konditsion_amount ?? 0) : null);
            });
    }

    private function getDataByMuvofiqlik(Request $request)
    {
        // Mass is counted from bales, as in the factory report: SUM(akt.amount - tara)
        return $this->buildBaseQuery($request)
            ->leftJoin('test_programs', 'applications.id', '=', 'test_programs.app_id')
            ->leftJoin('dalolatnoma', 'test_programs.id', '=', 'dalolatnoma.test_program_id')
            ->leftJoin('final_results', 'dalolatnoma.id', '=', 'final_results.dalolatnoma_id')
            ->leftJoin('sertificates', 'final_results.id', '=', 'sertificates.final_result_id')
            ->addSelect([
                DB::raw('MIN(sertificates.id) as certificate_id'),
                DB::raw('GROUP_CONCAT(DISTINCT sertificates.reestr_number SEPARATOR ", ") as certificate_number'),
                DB::raw('(SELECT SUM(akt.amount - d2.tara) FROM test_programs tp2
                    JOIN dalolatnoma d2 ON d2.test_program_id = tp2.id
                    JOIN akt_amount akt ON akt.dalolatnoma_id = d2.id
                    WHERE tp2.app_id = applications.id) as amount'),
            ])
            ->groupBy(
                'applications.id', 'applications.date', 'crop_data.party_number', 'crop_data.year',
                'crops_name.name', 'organization_companies.id', 'organization_companies.name'
            )
            ->get()
            ->map(fn ($row) => $this->formatRow($row, $row->certificate_number, (float) $row->amount, null));
    }

    private function formatRow($row, $certificateNumber, float $amount, $konditsionAmount): array
    {
        return [
            'id' => $row->id,
            'date' => $row->date,
            'party_number' => $row->party_number,
            'year' => $row->year,
            'product' => $row->product,
            'organization_id' => $row->organization_id,
            'organization' => $row->organization,
            'certified' => $row->certificate_id !== null,
            'certificate_number' => $certificateNumber,
            'amount' => round($amount, 2),
            'konditsion_amount' => $konditsionAmount,
        ];
    }

    /**
     * Same filters as FactoryByReportController::buildBaseQuery, so totals match the factory row.
     */
    private function buildBaseQuery(Request $request)
    {
        $query = DB::table('applications')
            ->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
            ->leftJoin('crops_name', 'crop_data.name_id', '=', 'crops_name.id')
            ->leftJoin('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
            ->select([
                'applications.id',
                'applications.date',
                'crop_data.party_number',
                'crop_data.year',
                'crops_name.name as product',
                'organization_companies.id as organization_id',
                'organization_companies.name as organization',
            ])
            ->where('applications.prepared_id', $request->input('factoryId'))
            ->where('crop_data.year', getCurrentYear())
            ->where('applications.app_type', getApplicationType())
            ->whereNull('applications.deleted_at')
            ->orderByDesc('applications.date')
            ->orderByDesc('applications.id');

        if ($request->input('start_date')) {
            $query->where('applications.date', '>=', $request->input('start_date'));
        }
        if ($request->input('end_date')) {
            $query->where('applications.date', '<=', $request->input('end_date'));
        }

        return $query;
    }
}
