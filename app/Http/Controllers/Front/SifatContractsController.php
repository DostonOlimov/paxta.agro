<?php

namespace App\Http\Controllers\Front;

use App\Filters\V1\DalolatnomaFilter;
use App\Filters\V1\SifatContractsFilter;
use App\Http\Controllers\Controller;
use App\Models\OrganizationCompanies;
use App\Models\SifatContracts;
use App\Services\AttachmentService;
use App\Services\SearchService;
use Illuminate\Http\Request;


class SifatContractsController extends Controller
{
    private $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    //search
    public function list(Request $request, SifatContractsFilter $filter,SearchService $service)
    {
        try {

            $names = getCropsNames();
            $states = getRegions();
            $years = getCropYears();

            return $service->search(
                $request,
                $filter,
                SifatContracts::class,
                [
                    'organization'
                ],
                compact('names', 'states', 'years'),
                'sifat_contracts.list',
                [],
                false
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], 404);
        }
    }


    // application addform
    public function add(Request $request)
    {
        $company = null;
        $company_id = $request->input('company_id');
        if($company_id){
            $company = OrganizationCompanies::find($company_id);
        }

        return view('sifat_contracts.add',[
            'company' =>$company
        ]);

    }

    //  store
    public function store(Request $request)
    {
        $sert = SifatContracts::create([
            'organization_id'       => $request->input('organization'),
            'number'    => $request->input('number'),
            'date'      => join('-', array_reverse(explode('-', $request->input('given_date')))),
        ]);

        if ($request->hasFile('reason-file')) {
            $this->attachmentService->upload($request->file('reason-file'), $sert);
        }

        if($request->input('company_id')){
            return redirect()->route('sifat-sertificates.add', $request->input('company_id'))
                ->with('message', 'Successfully Submitted');
        }else{
            return redirect()->route('sifat_contracts.list')->with('message', 'Successfully Submitted');
        }
    }

}

