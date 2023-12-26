<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CropData;
use App\Models\CropsGeneration;
use App\Models\CropsType;
use App\Models\Region;
use App\Models\CropsName;
use App\tbl_states;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class CropsNameController extends Controller
{
    public function index()
    {
        $title = 'Urug\'lik nomini qo\'shish';
        return view('crops_name.add', compact('title'));
    }

    // vehiclebrand list
    public function list()
    {
        $title = 'Urug\'lik ekin nomlari';
        $crops = CropsName::orderBy('id')->get();
        return view('crops_name.list', compact('crops','title'));
    }

    // vehiclebrand store
    public function store(Request $request)
    {
        $name = $request->input('name');
        $count = DB::table('crops_name')
            ->where('name', '=', $name)
            ->count();
        if ($count == 0) {
            $crop = new CropsName();
            $crop->name = $name;
            $crop->kodtnved = $request->input('tnved');
            $crop->save();
            return redirect('crops_name/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('crops_name/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', User::class);
        $app = CropData::where('name_id',$id)->first();
        if($app){
            return redirect('crops_name/list')->with('message', 'Cannot Deleted');
        }
        if(CropsGeneration::where('crop_id','=',$id)->get()){
            CropsGeneration::where('crop_id','=',$id)->delete();
        }
        if(CropsType::where('crop_id','=',$id)->get()){
            CropsType::where('crop_id','=',$id)->delete();
        }
        CropsName::destroy($id);
        return redirect('crops_name/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        return view('crops_name.edit', [
            'crops' => CropsName::findOrFail($id),
            'editid' => $id,
        ]);
    }

    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $crop = CropsName::findOrFail($id);
        $crop->name = $request->input('name');
        $crop->kodtnved = $request->input('tnved');
        $crop->save();

        return redirect('crops_name/list')->with('message', 'Successfully Updated');
    }
}
