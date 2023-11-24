<?php

namespace App\Http\Controllers;


use App\Models\Indicator;
use App\tbl_states;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class IndicatorController extends Controller
{
    public function index()
    {
        $title = 'Sifat ko\'rsatkichi qo\'shish';
        $crops = DB::table('crops_name')->get()->toArray();
        return view('indicator.add', compact('title','crops'));
    }

    //  list
    public function list()
    {
        $title = 'Sifat ko\'rsatkichlari';
        $types = Indicator::with('crops')->orderBy('id')->get();
        return view('indicator.list', compact('types','title'));
    }

    //  store
    public function store(Request $request)
    {
        $name = $request->input('name');
        $nd_name = $request->input('nd_name');
        $crop = $request->input('crop');
        $count = DB::table('quality_indacators')
            ->where('name', '=', $name)
            ->where('crop_id','=',$crop)
            ->count();
        if ($count == 0) {
            $type = new Indicator();
            $type->name = $name;
            $type->nd_name = $nd_name;
            $type->crop_id = $crop;
            $type->save();
            return redirect('indicator/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('indicator/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', User::class);
        Indicator::destroy($id);
        return redirect('indicator/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $crops = DB::table('crops_name')->get()->toArray();
        return view('indicator.edit', [
            'type' => Indicator::findOrFail($id),
            'editid' => $id,
            'crops' => $crops
        ]);
    }

    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $type = Indicator::findOrFail($id);
        $type->name = $request->input('name');
        $type->crop_id = $request->input('crop');
        $type->save();

        return redirect('indicator/list')->with('message', 'Successfully Updated');
    }
}
