<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Decision;
use App\Repositories\ActivityRepository;
use App\Repositories\DecisionRepository;
use App\Repositories\LaboratoryRepository;
use App\Repositories\TestProgramRepository;

class ApplicationObserver
{
    protected $activityRepository;
    protected $laboratoryRepository;
    protected $decisionRepository;
    protected $testProgramRepository;

    public function __construct(
        ActivityRepository $activityRepository,
        LaboratoryRepository $laboratoryRepository,
        DecisionRepository $decisionRepository,
        TestProgramRepository $testProgramRepository
    )
    {
        $this->activityRepository = $activityRepository;
        $this->laboratoryRepository = $laboratoryRepository;
        $this->decisionRepository = $decisionRepository;
        $this->testProgramRepository = $testProgramRepository;
    }

    /**
     * Handle the Application "created" event.
     *
     * @param Application $application
     * @return void
     */
    public function created(Application $application)
    {

        $this->activityRepository->logActivity([
            'ip_adress'   => request()->ip(),
            'user_id'     => $application->created_by,
            'action_id'   => $application->id,
            'action_type' => 'app_add',
            'action'      => "Ariza yaratildi",
            'time'        => now(),
        ]);


        if (isSifatSertificate()) {
            $stateId = optional($application->prepared)->state_id;
            if ($lab = $this->laboratoryRepository->findLaboratoryByStateId($stateId)) {
                $this->decisionRepository->create([
                    'app_id'       => $application->id,
                    'director_id'  => $lab->director_id,
                    'number'       => $application->id,
                    'laboratory_id'=> $lab->id,
                    'created_by'   => $application->created_by,
                    'date'         => $application->date,
                    'status'       => Decision::STATUS_NEW,
                ]);

                $this->testProgramRepository->create([
                    'app_id'      => $application->id,
                    'director_id' => $lab->director_id,
                ]);
            }
        }

    }

    /**
     * Handle the Application "updated" event.
     *
     * @param Application $application
     * @return void
     */
    public function updated(Application $application)
    {
        //
    }

    /**
     * Handle the Application "deleted" event.
     *
     * @param Application $application
     * @return void
     */
    public function deleted(Application $application)
    {
        //
    }

    /**
     * Handle the Application "restored" event.
     *
     * @param Application $application
     * @return void
     */
    public function restored(Application $application)
    {
        //
    }

    /**
     * Handle the Application "force deleted" event.
     *
     * @param Application $application
     * @return void
     */
    public function forceDeleted(Application $application)
    {
        //
    }
}
