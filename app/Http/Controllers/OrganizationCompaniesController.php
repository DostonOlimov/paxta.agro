<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Area;
use App\Models\Region;
use App\Models\OrganizationCompanies;
use App\tbl_states;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\tbl_activities;

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
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
            $states = DB::table('tbl_states')->where('id','=',$user->state_id)
                ->where('country_id', '=', 234)
                ->get()
                ->toArray();
            $cities = DB::table('tbl_cities')->where('state_id','=',$user->state_id)
                ->get()
                ->toArray();
        }
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
        $companies = $companies->paginate(50);
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

            if ($user->branch_id == \App\Models\User::BRANCH_STATE ) {
                $user_city = $user->state_id;
                $owners->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', $user_city);

                });
            }

            $owners = $owners->take(15)->get()->toArray();

            if (!empty($owners)) {
                return response()->json($owners);
            } else {
                return 'Nothing to show';
            }
        }
    }

    public function myorganizationadd()
    {
        $title = 'Buyurtmachi korxona qo\'shish';
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        $cities = '';

        $model = new OrganizationCompanies();
        return view('front.organization.add', compact('title','states','cities','model'));
    }

    public function myorganizationstore(Request $request)
    {
        $userA = Auth::user();
        $name = $request->input('name');
        $inn = $request->input('inn');
        $city = $request->input('city');
        $mobile = $request->input('mobile');
        $address = $request->input('address');
        $owner_name = $request->input('owner_name');

        $count = DB::table('organization_companies')
            ->where('inn','=',$inn)
            ->first();
        if (!$count) {
            $compy = new OrganizationCompanies();
            $compy->name = $name;
            $compy->city_id = $city;
            $compy->phone_number = $mobile;
            $compy->address = $address;
            $compy->owner_name = $owner_name;
            $compy->inn = $inn;
            $compy->save();

            $active = new tbl_activities;
            $active->ip_adress = $_SERVER['REMOTE_ADDR'];
            $active->user_id = $userA->id;
            $active->action_id = $compy->id;
            $active->action_type = 'organization_add';
            $active->action = "Korxona  qo'shildi";
            $active->time = date('Y-m-d H:i:s');
            $active->save();

            return redirect('prepared/my-prepared-add/'.$compy->id)->with('message', 'Successfully Submitted');
        } else {
            return redirect('prepared/my-prepared-add/'.$count->id)->with('message', 'Successfully Submitted');
        }
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
