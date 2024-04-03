<?php

namespace App\Http\Controllers;


use App\Models\Application;
use App\Models\CropData;
use App\Models\InXaus;
use App\Models\InXausValue;
use App\Models\OrganizationCompanies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InXausController extends Controller
{

    public function in_xaus_list(Request $request)
    {

        $user = Auth::User();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps = InXaus::with('in_xaus_value')
            ->with('user');

        if($user->role == \App\Models\User::STATE_EMPLOYEE){

            $apps = $apps->where('state_id', '=', $user->state_id);

        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps = $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }
        if ($city) {
            $apps = $apps->where('state_id', '=', $city);
        }

        $apps = $apps->get();

        return view('in_xaus.list', compact('apps','from','till','city','crop'));
    }


    // in_xaus addform

    public function add()
    {
        $types = InXaus::getType();

        return view('in_xaus.add',compact('types'));

    }


    // in_xaus store

    public function store(Request $request)
    {
        $userA = Auth::user();

        $dateOfBirth = join('-', array_reverse(explode('-', $request->input('date'))));

        $types = ['MIC','STR', 'INF', 'LEN']; // Assuming these are your types

        // Prepare the data array for batch insertion
        $inXausValueData = [];

        $inXaus = new InXaus();
        $inXaus->date = $dateOfBirth;
        $inXaus->state_id = $userA->state_id;
        $inXaus->created_by = $userA->id;
        $inXaus->save();

        // Retrieve the id of the newly created InXaus record
        $inXausId = $inXaus->id;
        foreach ($types as $key => $type) {
            // Populate InXausValue data for this InXaus record
            for ($i = 1; $i <= 10; $i++) {
                $inXausValueData[] = [
                    'in_xaus_id' => $inXausId, // Link InXausValue to the InXaus record
                    'value' => $request->input(strtolower($type) . $i),
                    'type' => $key+1
                ];
            }
        }

        InXausValue::insert($inXausValueData);

        return redirect('/in_xaus/list')->with('message', 'Successfully Submitted');

    }

    // in_xaus edit

    public function edit($id)
    {
        $editid = $id;
        $app = InXaus::find($editid);
        $values = $app->in_xaus_value->groupBy('type');

        return view('in_xaus.edit', compact('app', 'values'));
    }


    // in_xaus update

    public function update($id, Request $request)
    {
        $userA = Auth::user();
        $dateOfBirth = join('-', array_reverse(explode('-', $request->input('dob'))));

        $types = ['MIC','STR', 'INF', 'LEN']; // Assuming these are your types

        // Prepare the data array for batch insertion
        $inXausValueData = [];

        $inXaus = InXaus::find($id);
        $inXaus->date = $dateOfBirth;
        $inXaus->save();
        InXausValue::where('in_xaus_id',$id)->delete();

        foreach ($types as $key => $type) {
            // Populate InXausValue data for this InXaus record
            for ($i = 1; $i <= 10; $i++) {
                $inXausValueData[] = [
                    'in_xaus_id' => $id, // Link InXausValue to the InXaus record
                    'value' => $request->input(strtolower($type) . $i),
                    'type' => $key+1
                ];
            }
        }

        InXausValue::insert($inXausValueData);

        return redirect('/in_xaus/list')->with('message', 'Successfully Submitted');
    }

    public function view($id)
    {
        $user = InXaus::with('in_xaus_value')->findOrFail($id);
        $values = $user->in_xaus_value->groupBy('type');

        return view('in_xaus.show', compact('user','values'));
    }

    public function view2($i,$id)
    {
        $in_xaus = InXausValue::where('in_xaus_id', $id)
            ->where('type',$i);

        $values = $in_xaus->get();
        $avg_value = $in_xaus->avg('value');

        return view('in_xaus.show2', compact('values','id','avg_value'));
    }

}

