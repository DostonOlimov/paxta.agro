<?php

namespace App\Http\Controllers;

use App\Models\Nds;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NdsController extends Controller
{
    public function index()
    {
        $title = 'Normativ hujjat qo\'shish';
        $crops = DB::table('crops_name')->get()->toArray();
        $types = Nds::getType();
        return view('nds.add', compact('title','crops','types'));
    }

    //list
    public function list()
    {
        $title = 'Normativ hujjatlar';
        $nds = Nds::with('crops')->orderBy('id')->get();
        return view('nds.list', compact('nds','title'));
    }

    //  store
    public function store(Request $request)
    {
        $type = $request->input('type');
        $number = $request->input('number');
        $name = $request->input('name');
        $crop = $request->input('crop');
        $count = DB::table('nds')
            ->where('crop_id','=',$crop)
            ->count();
        if ($count == 0) {
            $nd = new Nds();
            $nd->type_id = $type;
            $nd->number = $number;
            $nd->name = $name;
            $nd->crop_id = $crop;
            $nd->save();
            return redirect('nds/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('nds/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', User::class);
        Nds::destroy($id);
        return redirect('nds/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $crops = DB::table('crops_name')->get()->toArray();
        return view('nds.edit', [
            'nd' => Nds::findOrFail($id),
            'editid' => $id,
            'crops' => $crops,
            'types' => Nds::getType()
        ]);
    }

    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $type = Nds::findOrFail($id);
        $type->type_id = $request->input('type');
        $type->number = $request->input('number');
        $type->name = $request->input('name');
        $type->crop_id = $request->input('crop');
        $type->save();

        return redirect('nds/list')->with('message', 'Successfully Updated');
    }
}
