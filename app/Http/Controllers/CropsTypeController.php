<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CropData;
use App\Models\Region;
use App\Models\CropsType;
use App\tbl_states;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class CropsTypeController extends Controller
{
    public function index()
    {
        $title = 'Urug\' navini qo\'shish';
        $crops = DB::table('crops_name')->get()->toArray();
        return view('crops_type.add', compact('title','crops'));
    }

    // vehiclebrand list
    public function list()
    {
        $title = 'Uru\'glik navlari';
        $types = CropsType::with('crops')->orderBy('id')->get();
        return view('crops_type.list', compact('types','title'));
    }

    // vehiclebrand store
    public function store(Request $request)
    {
        $name = $request->input('name');
        $crop = $request->input('crop');
        $kod = $request->input('kod');
        $count = DB::table('crops_type')
            ->where('name', '=', $name)
            ->where('crop_id','=',$crop)
            ->count();
        if ($count == 0) {
            $type = new CropsType();
            $type->name = $name;
            $type->crop_id = $crop;
            $type->kod = $kod;
            $type->save();
            return redirect('crops_type/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('crops_type/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', User::class);
        $app = CropData::where('type_id',$id)->first();
        if($app){
            return redirect('crops_type/list')->with('message', 'Cannot Deleted');
        }
        CropsType::destroy($id);
        return redirect('crops_type/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $crops = DB::table('crops_name')->get()->toArray();
        return view('crops_type.edit', [
            'type' => CropsType::findOrFail($id),
            'editid' => $id,
            'crops' => $crops
        ]);
    }

    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $type = CropsType::findOrFail($id);
        $type->name = $request->input('name');
        $type->crop_id = $request->input('crop');
        $type->kod = $request->input('kod');
        $type->save();

        return redirect('crops_type/list')->with('message', 'Successfully Updated');
    }
}
