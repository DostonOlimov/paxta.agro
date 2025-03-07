<?php

namespace App\Http\Controllers;

use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\CropData;
use App\Services\ModelServices\ApplicationService;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Requests\RejectApplicationRequest;
use App\Services\SearchService;
use App\Services\Telegram\TelegramService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        parent::__construct();
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of applications with filtering and search capabilities
     *
     * @param Request $request
     * @param ApplicationFilter $filter
     * @param SearchService $service
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function applicationList(Request $request, ApplicationFilter $filter, SearchService $service)
    {
        $this->authorize('viewAny', Application::class);

        try {
            $viewData = $this->getCommonViewData();
            $viewData['all_status'] = getAppStatus();
            $viewData['names'] = getCropsNames();
            $viewData['states'] = getRegions();
            $viewData['years'] = getCropYears();

            return $service->search(
                $request,
                $filter,
                Application::class,
                ['crops', 'organization', 'prepared', 'crops.name', 'organization.area.region'],
                $viewData,
                'application.list'
            );
            } catch (\Throwable $e) {
            $this->handleException('applicationList', $e);
            return response()->view('errors.500', [], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    /**
     * Display form for creating a new application
     *
     * @throws AuthorizationException
     */
    public function addApplication()
    {
        $this->authorize('create', Application::class);
        return view('application.add', $this->getCommonViewData(['year' => getCurrentYear()]));
    }

    /**
     * Store a newly created application
     * @param StoreApplicationRequest $request
     * @return RedirectResponse
     */
    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        try {
            $this->authorize('create', Application::class);
            $this->applicationService->storeApplication($request);
            return redirect()->route('application.list')
                ->with('message', 'Application successfully submitted');
        } catch (\Throwable $e) {
            return $this->handleRedirectException('applicationStore', $e, 'Failed to submit application');
        }
    }

    /**
     * Display form for editing an application
     *
     * @param Application $application
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(Application $application)
    {
        $this->authorize('edit', $application);
        return view('application.edit', array_merge(
            ['app' => $application],
            $this->getCommonViewData()
        ));
    }

    /**
     * Update an existing application
     * @param $id
     * @param UpdateApplicationRequest $request
     * @return RedirectResponse
     */
    public function update($id, UpdateApplicationRequest $request): RedirectResponse
    {
        try {
            $this->applicationService->updateApplication($id, $request);
            return redirect()->route('application.list')
                ->with('message', 'Application successfully updated');
        } catch (\Throwable $e) {
            return $this->handleRedirectException('applicationUpdate', $e, 'Failed to update application');
        }
    }

    /**
     * Display a specific application
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     * @throws AuthorizationException
     */
    public function showApplication($id)
    {
        $this->authorize('view', Application::class);
        $application = Application::with('organization.city')->findOrFail($id);
        return view('application.show', [
            'app' => $application,
            'company' => $application->organization
        ]);
    }

    /**
     * Accept an application
     * @param Application $application
     * @return RedirectResponse
     */
    public function accept(Application $application): RedirectResponse
    {
        try {
            $this->authorize('update', $application);
            $this->applicationService->acceptApplication($application);
            return redirect()->route('application.list')
                ->with('message', 'Application successfully accepted');
        } catch (\Throwable $e) {
            return $this->handleRedirectException('applicationAccept', $e, 'Failed to accept application');
        }
    }

    /**
     * Display rejection form for an application
     *
     * @param Application $application
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     * @throws AuthorizationException
     */
    public function reject(Application $application)
    {
        $this->authorize('update', $application);
        return view('application.reject', ['app' => $application]);
    }

    /**
     * Process application rejection with reason
     * @param RejectApplicationRequest $request
     * @return RedirectResponse
     */
    public function rejectStore(RejectApplicationRequest $request): RedirectResponse
    {
        try {
            $application = Application::findOrFail($request->input('app_id'));
            $this->applicationService->rejectApplication($application, $request->input('reason'));
            return redirect()->route('application.list')
                ->with('message', 'Application rejected successfully');
        } catch (\Throwable $e) {
            return $this->handleRedirectException('applicationReject', $e, 'Failed to reject application');
        }
    }

    /**
     * Get common view data to avoid repetition
     * @param array $extra
     * @return array
     */
    protected function getCommonViewData(array $extra = []): array
    {
        return array_merge([
            'names' => getCropsNames(),
            'countries' => getCountries(),
            'measure_types' => CropData::getMeasureType(),
            'years' => CropData::getYear(),
        ], $extra);
    }

    /**
     * Handle exceptions with logging and Telegram notification
     * @param string $method
     * @param \Throwable $e
     */
    private function handleException(string $method, \Throwable $e): void
    {
        \Log::error("Error in {$method}: " . $e->getMessage(), ['exception' => $e]);
        (new TelegramService())->sendErrorMessage(
            "⚠️ *Error in {$method}!* \n\n 📌 *Message:* \"{$e->getMessage()}\""
        );
    }

    /**
     * Handle exceptions for redirect responses
     * @param string $method
     * @param \Throwable $e
     * @param string $errorMessage
     * @return RedirectResponse
     */
    private function handleRedirectException(string $method, \Throwable $e, string $errorMessage): RedirectResponse
    {
        $this->handleException($method, $e);
        return redirect()->back()->withErrors(['error' => "{$errorMessage}: {$e->getMessage()}"]);
    }
}

