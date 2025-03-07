<?php

namespace App\Services\ModelServices;

use App\Models\Application;
use App\Repositories\AppStatusChangesReposiroty;
use App\Repositories\CropDataRepository;
use App\Repositories\ApplicationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    protected CropDataRepository $cropDataRepository;
    protected ApplicationRepository $applicationRepository;
    protected AppStatusChangesReposiroty $appStatusChanges;


    public function __construct(
        CropDataRepository $cropDataRepository,
        ApplicationRepository $applicationRepository,
        AppStatusChangesReposiroty $appStatusChanges
    ) {
        $this->cropDataRepository = $cropDataRepository;
        $this->applicationRepository = $applicationRepository;
        $this->appStatusChanges = $appStatusChanges;
    }

    public function storeApplication($request)
    {
        $userId = Auth::user()->id;

        return DB::transaction(function () use ($userId, $request) {
            $crop = $this->cropDataRepository->create([
                'name_id'       => $request->input('name'),
                'kodtnved'      => $request->input('tnved'),
                'party_number'  => $request->input('party_number'),
                'measure_type'  => $request->input('measure_type'),
                'amount'        => $request->input('amount'),
                'year'          => $request->input('year'),
                'sxeme_number'  => $request->input('sxeme_number'),
                'toy_count'     => $request->input('toy_count'),
                'country_id'    => $request->input('country'),
            ]);

            return $application = $this->applicationRepository->create([
                'crop_data_id'     => $crop->id,
                'organization_id'  => $request->input('organization'),
                'prepared_id'      => $request->input('prepared'),
                'type'             => Application::TYPE_1,
                'date'             => formatDate($request->input('dob')),
                'data'             => $request->input('data'),
                'status'           => Application::STATUS_FINISHED,
                'app_type'         => getApplicationType(),
                'created_by'       => $userId,
            ]);
        });
    }

    public function updateApplication($id,$request)
    {
        $app = Application::findOrFail($id);
        return DB::transaction(function () use ($app, $request) {

            $this->cropDataRepository->update($app->crops->id,
                [
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

            return $application = $this->applicationRepository->update($app->id,
                [
                'organization_id' => $request->input('organization'),
                'prepared_id'     => $request->input('prepared'),
                 'date'           => formatDate($request->input('dob')),
                 'data'           => $request->input('data'),
            ]);
        });
    }

    public function acceptApplication(Application $app)
    {
        return $this->applicationRepository->update($app->id, [
            'status' => Application::STATUS_ACCEPTED,
            'progress' => Application::PROGRESS_ANSWERED,
            'accepted_date' => now(),
            'accepted_id' => Auth::id(),
        ]);
    }

    public function rejectApplication(Application $app, string $reason)
    {
        DB::transaction(function () use ($app, $reason) {
            $this->applicationRepository->update($app->id, [
                'status' => Application::STATUS_REJECTED,
            ]);

            $this->appStatusChanges::create([
                'app_id' => $app->id,
                'status' => Application::STATUS_REJECTED,
                'comment' => $reason,
                'user_id' => Auth::id(),
            ]);
        });
    }
}
