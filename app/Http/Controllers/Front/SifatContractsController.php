<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\OrganizationCompanies;
use App\Models\SifatContracts;
use App\Services\AttachmentService;
use Illuminate\Http\Request;


class SifatContractsController extends Controller
{
    private $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function list(Request $request)
    {
        $query = SifatContracts::with(['organization', 'attachment'])
            ->orderByDesc('date');

        if ($search = $request->input('search')) {
            $query->where('number', 'like', '%' . $search . '%')
                ->orWhereHas('organization', fn($q) => $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('inn', 'like', '%' . $search . '%'));
        }

        $contracts = $query->paginate(50)->withQueryString();

        return view('sifat_contracts.list', compact('contracts'));
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
        $request->validate([
            'organization'      => 'required|exists:organization_companies,id',
            'number'            => 'required|string|max:255',
            'given_date'        => 'required|date',
            'reason-file'       => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        // Delete existing SifatContracts with the same organization_id
        SifatContracts::where('organization_id', $request->input('organization'))->delete();

        $sert = SifatContracts::create([
            'organization_id' => $request->input('organization'),
            'number'          => $request->input('number'),
            'date'            => join('-', array_reverse(explode('-', $request->input('given_date')))),
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

