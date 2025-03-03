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
     * Display a listing of applications with filtering and search.
     * @param Request $request
     * @param ApplicationFilter $filter
     * @param SearchService $service
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|\Illuminate\Http\Response
     */
    public function applicationList(Request $request, ApplicationFilter $filter, SearchService $service)
    {
        try {
            $data = $this->getCommonViewData();
            $data['all_status'] = getAppStatus();
            $names = getCropsNames();
            $states = getRegions();
            $all_status = $all_status = getAppStatus();
            $years = getCropYears();

            return $service->search(
                $request,
                $filter,
                Application::class,
                ['crops', 'organization', 'prepared', 'crops.name', 'organization.area.region'],
                compact('data','states','all_status','names','years'),
                'application.list',
                [],
                false,
                null,
                null,
                []
            );
        } catch (\Throwable $e) {
            Log::error('Error in applicationList: ' . $e->getMessage(), ['exception' => $e]);
            $telegramService = new TelegramService();
            $telegramService->sendErrorMessage("⚠️ *Error in applicationList!* \n\n 📌 *Message:* \"{$e->getMessage()}\"");
            return response()->view('errors.500', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new application.
     */
    public function addapplication()
    {
        return view('application.add', $this->getCommonViewData(['year' => getCurrentYear()]));
    }

    /**
     * Store a newly created application.
     * @param StoreApplicationRequest $request
     * @return RedirectResponse
     */
    public function store(StoreApplicationRequest $request)
    {
        try {
            $this->authorize('create', Application::class);
            $this->applicationService->storeApplication($request);
            return redirect()->route('application.list')->with('message', 'Successfully Submitted');
        } catch (\Exception $e) {
            Log::error('Error storing application: ' . $e->getMessage(), ['exception' => $e]);
            $telegramService = new TelegramService();
            $telegramService->sendErrorMessage("⚠️ *Error in applicationStore!* \n\n 📌 *Message:* \"{$e->getMessage()}\"");
            return redirect()->back()->withErrors(['error' => 'Failed to submit application: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing an application.
     */
    public function edit($id)
    {
        $app = Application::findOrFail($id);
        return view('application.edit', array_merge(
            ['app' => $app],
            $this->getCommonViewData()
        ));
    }

    /**
     * Update an existing application.
     * @param $id
     * @param UpdateApplicationRequest $request
     * @return RedirectResponse
     */
    public function update($id, UpdateApplicationRequest $request)
    {
        try {
            $this->authorize('update', Application::findOrFail($id));
            $this->applicationService->updateApplication($id, $request);
            return redirect()->route('application.list')->with('message', 'Successfully Updated');
        } catch (\Exception $e) {
            Log::error('Error updating application: ' . $e->getMessage(), ['exception' => $e]);
            $telegramService = new TelegramService();
            $telegramService->sendErrorMessage("⚠️ *Error in applicationUpdate!* \n\n 📌 *Message:* \"{$e->getMessage()}\"");
            return redirect()->back()->withErrors(['error' => 'Failed to update application: ' . $e->getMessage()]);
        }
    }

    /**
     * Display a specific application.
     */
    public function showapplication($id)
    {
        $app = Application::with('organization.city')->findOrFail($id);
        return view('application.show', ['app' => $app, 'company' => $app->organization]);
    }

    /**
     * Accept an application.
     */
    public function accept($id)
    {
        try {
            $app = Application::findOrFail($id);
            $this->authorize('update', $app);
            $this->applicationService->acceptApplication($app);
            return redirect()->route('application.list')->with('message', 'Successfully Accepted');
        } catch (\Exception $e) {
            Log::error('Error accepting application: ' . $e->getMessage(), ['exception' => $e]);
            $telegramService = new TelegramService();
            $telegramService->sendErrorMessage("⚠️ *Error in applicationAccept!* \n\n 📌 *Message:* \"{$e->getMessage()}\"");
            return redirect()->back()->withErrors(['error' => 'Failed to accept application']);
        }
    }

    /**
     * Show the rejection form for an application.
     */
    public function reject($id)
    {
        $app = Application::findOrFail($id);
        return view('application.reject', compact('app'));
    }

    /**
     * Reject an application with a reason.
     * @param RejectApplicationRequest $request
     * @return RedirectResponse
     */
    public function reject_store(RejectApplicationRequest $request)
    {
        try {
            $app = Application::findOrFail($request->input('app_id'));
            $this->authorize('accept', $app);
            $this->applicationService->rejectApplication($app, $request->input('reason'));
            return redirect()->route('application.list')->with('message', 'Application Rejected Successfully');
        } catch (\Exception $e) {
            Log::error('Error rejecting application: ' . $e->getMessage(), ['exception' => $e]);
            $telegramService = new TelegramService();
            $telegramService->sendErrorMessage("⚠️ *Error in applicationReject!* \n\n 📌 *Message:* \"{$e->getMessage()}\"");
            return redirect()->back()->withErrors(['error' => 'Failed to reject application']);
        }
    }

    /**
     * Get common data for views to avoid duplication.
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
}

