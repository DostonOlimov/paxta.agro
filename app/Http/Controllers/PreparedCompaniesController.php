<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\PreparedCompanies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreparedCompaniesController extends Controller
{

    public function add($redirect_id)
    {
        $user = Auth::User();
        $title = 'Korxona qo\'shish';
        $regions = DB::table('tbl_states')->where('country_id', '=', 234)
            ->get()
            ->toArray();
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
            $regions = DB::table('tbl_states')->where('id','=',$user->state_id)
                ->where('country_id', '=', 234)
                ->get()
                ->toArray();
        }
        return view('prepared.add', compact('title','regions','redirect_id'));
    }

    // vehiclebrand list
    public function list()
    {
        $user = Auth::User();
        $title = 'Urug‘lik tayorlangan shaxobcha yoki sex nomi';
        $companies = PreparedCompanies::with('region');
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
            $user_city = $user->state_id;
            $companies = $companies->whereHas('region', function ($query) use ($user_city) {
                $query->where('state_id', '=', $user_city);
            });
        }
        $companies = $companies->orderBy('id','desc')->get();
        return view('prepared.list', compact('companies','title'));
    }

    // vehiclebrand store
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $name = $request->input('name');
        $region = $request->input('region');
        $kod = $request->input('kod');
        $tara = $request->input('tara');
        $count = DB::table('prepared_companies')
            ->where('name', '=', $name)
            ->where('state_id','=',$region)
            ->count();
        if ($count == 0) {
            $cityname = new PreparedCompanies();
            $cityname->name = $name;
            $cityname->state_id = $region;
            $cityname->kod = $kod ?? 10000;
            $cityname->tara = $tara ?? 0;
            $cityname->save();
            if($request->input('redirect_id') == 2){
                return redirect('application/add')->with('message', 'Successfully Submitted');
            }
            return redirect('prepared/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('prepared/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('delete', User::class);
        $app = Application::where('prepared_id',$id)->first();
        if($app){
            return redirect('prepared/list')->with('message', 'Cannot Deleted');
        }
        PreparedCompanies::destroy($id);
        return redirect('prepared/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $user = Auth::User();
        $regions = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
            $regions = DB::table('tbl_states')->where('id','=',$user->state_id)
                ->where('country_id', '=', 234)
                ->get()
                ->toArray();
        }
        return view('prepared.edit', [
            'company' => PreparedCompanies::findOrFail($id),
            'editid' => $id,
            'regions' => $regions,
        ]);
    }

    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $this->authorize('update', User::class);
        $state = PreparedCompanies::findOrFail($id);
        $state->name = $request->input('name');
        $state->kod = $request->input('kod');
        $state->tara = $request->input('tara');
        $state->state_id = $request->input('region');
        $state->save();

        return redirect('prepared/list')->with('message', 'Successfully Updated');
    }
    public function search(Request $request)
    {
        $user = auth()->user();
        $ownername = $request->input('search');

        if (!empty($ownername)) {
            $owners = DB::table('prepared_companies')
                ->select('id', 'name','kod')
                ->where(function ($query) use ($ownername) {
                    $query->where('name', 'like', '%' . $ownername . '%')
                        ->orWhere('kod', 'like','%' . $ownername . '%');
                });

            if ($user->branch_id == \App\Models\User::BRANCH_STATE ) {
                $owners->where('state_id', $user->state_id);
            }

            $owners = $owners->take(15)->get()->toArray();

            if (!empty($owners)) {
                return response()->json($owners);
            } else {
                return 'Nothing to show';
            }
        }
    }

    public function mypreparedadd($organization_id)
    {
        $title = 'Korxona qo\'shish';
        $type = Application::getType();
        $regions = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('front.prepared.add', compact('title','regions','organization_id','type'));
    }

    public function mypreparedstore(Request $request)
    {
        $organization = $request->input('organization');
        $type = $request->input('app_type');
        $name = $request->input('name');
        $region = $request->input('region');
        $count = DB::table('prepared_companies')
            ->where('name', '=', $name)
            ->where('state_id','=',$region)
            ->first();
        if (!$count) {
            $cityname = new PreparedCompanies();
            $cityname->name = $name;
            $cityname->state_id = $region;
            $cityname->save();
            $prepared = $cityname->id;
        }else{
            $prepared = $count->id;
        }

        return redirect("application/my-application-add?organization=$organization&type=$type&prepared=$prepared")->with('message', 'Duplicate Data');

    }
}
