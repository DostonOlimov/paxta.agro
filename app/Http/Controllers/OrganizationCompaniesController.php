<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Area;
use App\Models\DefaultModels\tbl_activities;
use App\Models\OrganizationCompanies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrganizationCompaniesController extends Controller
{
    public function add($redirect_id)
    {
        $title = 'Buyurtmachi korxona qo\'shish';
        $user = Auth::User();
        $states = DB::table('tbl_states')->where('country_id', '=', 234)
            ->get()
            ->toArray();
        $cities = '';
//        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
//            $states = DB::table('tbl_states')->where('id','=',$user->state_id)
//                ->where('country_id', '=', 234)
//                ->get()
//                ->toArray();
//            $cities = DB::table('tbl_cities')->where('state_id','=',$user->state_id)
//                ->get()
//                ->toArray();
//        }
        $model = new OrganizationCompanies();
        return view('organization.add', compact('title','states','cities','model','redirect_id'));
    }

    // vehiclebrand list
    public function list()
    {
        $title = 'Buyurtmachi korxona yoki klasterlar';
        $user = Auth::User();
        $companies = OrganizationCompanies::latest('id')
        ->with('city')
        ->with('city.region');
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
            $user_city = $user->state_id;
            $companies = $companies->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
            });
        }
        $companies = $companies->get();
        return view('organization.list', compact('companies','title'));
    }

    public function store(Request $request)
    {

        $this->authorize('create', User::class);
        $name = $request->input('name');
        $inn = $request->input('inn');
        $city = $request->input('city');
        $mobile = $request->input('mobile');
        $address = $request->input('address');
        $owner_name = $request->input('owner_name');

        $count = DB::table('organization_companies')
            ->where('inn','=',$inn)
            ->where('name','=',$name)
            ->count();
        if ($count == 0) {
            $compy = new OrganizationCompanies();
            $compy->name = $name;
            $compy->city_id = $city;
            $compy->phone_number = $mobile;
            $compy->address = $address;
            $compy->owner_name = $owner_name;
            $compy->inn = $inn;
            $compy->save();
            if($request->input('redirect_id') == 2){
               return redirect('application/add')->with('message', 'Successfully Submitted');
            }else{
                return redirect('organization/list')->with('message', 'Successfully Submitted');
            }
        } else {
            return redirect('organization/add/1')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('delete', User::class);
        $app = Application::where('organization_id',$id)->first();
        if($app){
            return redirect('organization/list')->with('message', 'Cannot Deleted');
        }
        OrganizationCompanies::destroy($id);
        return redirect('organization/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $user = Auth::User();
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
            $states = DB::table('tbl_states')->where('id','=',$user->state_id)
                ->where('country_id', '=', 234)
                ->get()
                ->toArray();
        }
        return view('organization.edit', [
            'company' => OrganizationCompanies::with('city')->findOrFail($id),
            'editid' => $id,
            'states' => $states,
            'cities' => ''
        ]);
    }
    public function show($id)
    {
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('organization.show', [
            'company' => OrganizationCompanies::with('city')->findOrFail($id),
            'editid' => $id,
            'states' => $states,
            'cities' => ''
        ]);
    }
    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $compy = OrganizationCompanies::findOrFail($id);
        $this->authorize('update', $compy);
        $compy->name = $request->input('name');
        $compy->city_id =  $request->input('city');
        $compy->phone_number = $request->input('mobile');
        $compy->address = $request->input('address');
        $compy->owner_name = $request->input('owner_name');
        $compy->inn = $request->input('inn');
        $compy->save();
        return redirect('organization/list')->with('message', 'Successfully Updated');
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $ownername = $request->input('search');

        if ($ownername !== '') {
            $owners = OrganizationCompanies::select('id', 'name', 'inn')
                ->where(function ($query) use ($ownername) {
                    $query->where('name', 'like', '%' . $ownername . '%')
                        ->orWhere('inn', 'like', '%' . $ownername . '%');
                });

//            if ($user->branch_id == \App\Models\User::BRANCH_STATE ) {
//                $user_city = $user->state_id;
//                $owners->whereHas('city', function ($query) use ($user_city) {
//                    $query->where('state_id', $user_city);
//
//                });
//            }

            $owners = $owners->take(15)->get()->toArray();

            if (!empty($owners)) {
                return response()->json($owners);
            } else {
                return 'Nothing to show';
            }
        }
    }

    public function myorganizationadd(Request $request)
    {
        $showId = $request->input('showId');
        $user = Auth::user();
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();

        $cities = '';
        if(count($states) == 1){
            $cities = DB::table('tbl_cities')->where('state_id', '=', $states[0]->id)->get();
        }
        $company = null;

        if($showId == 1){
            return view('front.organization.add2', compact( 'states', 'showId','cities', 'company','user'));

        }else{
            return view('front.organization.add', compact( 'states', 'showId','cities', 'company','user'));
        }
    }

    public function myorganizationstore(Request $request)
    {
        $user = Auth::user();
        $inn = $request->input('inn');
        $showId = $request->input('showId');

        // Define validation rules with camelCase attribute names
        $validatedData = $request->validate([
            'inn' => 'required|int|digits:9',
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:150',
            'owner_name' => 'required|string|max:50',
            'phone_number' => 'required|string|max:20',
        ]);

        // Check if organization already exists
        $company = OrganizationCompanies::where('inn', $inn)->first();

        if (!$company) {
            // Create a new organization
            $company = OrganizationCompanies::create([
                'name' => $request->input('name'),
                'city_id' => $request->input('city_id'),
                'phone_number' => $request->input('phone_number'),
                'address' => $request->input('address'),
                'owner_name' => $request->input('owner_name'),
                'inn' => $inn,
            ]);

            // Log the activity
            tbl_activities::create([
                'ip_adress' => $request->ip(),
                'user_id' => $user->id,
                'action_id' => $company->id,
                'action_type' => 'organization_add',
                'action' => "Korxona qo'shildi",
                'time' => now(),
            ]);
        }

        $route = $company->sifat_contract
            ? 'sifat-sertificates.add'
            : ($showId == 1 ? 'sifat-sertificates2.add' : 'sifat_contracts.add');

        $param = $route === 'sifat_contracts.add' ? ['company_id' => $company->id] : $company->id;

        return redirect()->route($route, $param)->with('message', 'Successfully Submitted');
    }

    public function myorganizationview ($id)
    {
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('front.organization.show', [
            'company' => OrganizationCompanies::with('city')->findOrFail($id),
            'editid' => $id,
            'states' => $states,
            'cities' => ''
        ]);
    }
    public function myorganizationedit ($id)
    {
        $company = OrganizationCompanies::with('city')->find($id);
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('front.organization.edit', [
            'company' => $company,
            'editid' => $id,
            'states' => $states,
            'cities' => ''
        ]);
    }
    public function myorganizationupdate(Request $request, $id)
    {
        $compy = OrganizationCompanies::findOrFail($id);
        $this->authorize('update', $compy);
        $compy->name = $request->input('name');
        $compy->city_id =  $request->input('city');
        $compy->phone_number = $request->input('mobile');
        $compy->address = $request->input('address');
        $compy->owner_name = $request->input('owner_name');
        $compy->inn = $request->input('inn');
        $compy->save();
        return redirect('application/my-applications')->with('message', 'Successfully Updated');
    }

}
