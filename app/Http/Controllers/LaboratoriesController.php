<?php

namespace App\Http\Controllers;

use App\Models\Laboratories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DefaultModels\tbl_activities;

class LaboratoriesController extends Controller
{
    public function add()
    {
        $title = 'Buyurtmachi korxona qo\'shish';
        $user = Auth::User();
        $states = DB::table('tbl_states')->where('country_id', '=', 234)
            ->get()
            ->toArray();
        $cities = '';
        if($user->role == \App\Models\User::STATE_EMPLOYEE){
            $states = DB::table('tbl_states')->where('id','=',$user->state_id)
                ->where('country_id', '=', 234)
                ->get()
                ->toArray();
            $cities = DB::table('tbl_cities')->where('state_id','=',$user->state_id)
                ->get()
                ->toArray();
        }
        $model = new Laboratories();
        return view('laboratories.add', compact('title','states','cities','model'));
    }

    // vehiclebrand list
    public function list()
    {
        $title = 'Laboratoriya nomlari';
        $user = Auth::User();
        $companies = Laboratories::latest('id')
            ->with('city')
            ->with('city.region');
        if($user->role == \App\Models\User::STATE_EMPLOYEE){
            $user_city = $user->state_id;
            $companies = $companies->whereHas('city', function ($query) use ($user_city) {
                $query->where('state_id', '=', $user_city);
            });
        }
        $companies = $companies->get();
        return view('laboratories.list', compact('companies','title'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $name = $request->input('name');
        $kod = $request->input('kod');
        $certifikate = $request->input('certifikate');
        $city = $request->input('city');
        $mobile = $request->input('mobile');
        $address = $request->input('address');

        $count = DB::table('laboratories')
            ->where('kod','=',$kod)
            ->count();
        if ($count == 0) {
            $compy = new Laboratories();
            $compy->name = $name;
            $compy->city_id = $city;
            $compy->phone_number = $mobile;
            $compy->address = $address;
            $compy->certificate = $certifikate;
            $compy->kod = $kod;
            $compy->save();

            return redirect('laboratories/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('laboratories/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('delete', User::class);
        Laboratories::destroy($id);
        return redirect('laboratories/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $user = Auth::User();
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        if($user->role == \App\Models\User::STATE_EMPLOYEE){
            $states = DB::table('tbl_states')->where('id','=',$user->state_id)
                ->where('country_id', '=', 234)
                ->get()
                ->toArray();
        }
        return view('laboratories.edit', [
            'company' => Laboratories::with('city')->findOrFail($id),
            'editid' => $id,
            'states' => $states,
            'cities' => ''
        ]);
    }
    public function show($id)
    {
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('laboratories.show', [
            'company' => Laboratories::with('city')->findOrFail($id),
            'editid' => $id,
            'states' => $states,
            'cities' => ''
        ]);
    }
    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $compy = Laboratories::findOrFail($id);
        $compy->name = $request->input('name');
        $compy->city_id =  $request->input('city');
        $compy->phone_number = $request->input('mobile');
        $compy->address = $request->input('address');
        $compy->certificate = $request->input('certificate');
        $compy->kod = $request->input('kod');
        $compy->save();
        return redirect('laboratories/list')->with('message', 'Successfully Updated');
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $ownername = $request->input('search');

        if ($ownername != '') {
            $owners = DB::table('laboratories_companies')
                ->select('id','name', 'inn');

            $owners = $owners->where(function($query) use($ownername){
                $query->where('name', 'like', '%'.$ownername.'%')
                    ->orWhere('inn', 'like', '%'.$ownername.'%');
            });
            $owners = $owners->take(15)->get()->toArray();

            if(!empty($owners)) {
                echo json_encode($owners);
            }else{
                echo 'Nothing to show';
            }

        }
    }


}
