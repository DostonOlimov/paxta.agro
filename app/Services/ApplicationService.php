<?php

namespace App\Services;

use App\Models\Application;
use App\Repositories\CropDataRepository;
use App\Repositories\ApplicationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    protected $cropDataRepository;
    protected $applicationRepository;


    public function __construct(
        CropDataRepository $cropDataRepository,
        ApplicationRepository $applicationRepository
    ) {
        $this->cropDataRepository = $cropDataRepository;
        $this->applicationRepository = $applicationRepository;
    }

    public function storeApplication($request)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($user, $request) {
            $crop = $this->cropDataRepository->create([
                'name_id'       => $request->input('name'),
                'country_id'    => $request->input('country'),
                'kodtnved'      => $request->input('tnved'),
                'party_number'  => $request->input('party_number'),
                'measure_type'  => $request->input('measure_type'),
                'amount'        => $request->input('amount'),
                'year'          => $request->input('year'),
                'toy_count'     => $request->input('toy_count'),
                'sxeme_number'  => $request->input('sxeme_number'),
            ]);

            return $application = $this->applicationRepository->create([
                'crop_data_id'     => $crop->id,
                'organization_id'  => $request->input('organization'),
                'prepared_id'      => $request->input('prepared'),
                'date'             => formatDate($request->input('dob')),
                'status'           => Application::STATUS_FINISHED,
                'data'             => $request->input('data'),
                'created_by'       => $user->id,
                'app_type'         => getCropType()
            ]);
        });
    }
}
