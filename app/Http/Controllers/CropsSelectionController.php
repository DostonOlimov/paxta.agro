<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use App\Models\CropsSelection;
use App\Models\User;
use Illuminate\Http\Request;

class CropsSelectionController extends Controller
{
    public function index()
    {
        $title = 'Urug\' avlodini qo\'shish';
        $crops = DB::table('crops_name')->get()->toArray();
        return view('crops_selection.add', compact('title','crops'));
    }


    public function list()
    {
        $title = 'Uru\'glik avlodlari';
        $types = CropsSelection::with('crops')->orderBy('id')->get();
        return view('crops_selection.list', compact('types','title'));
    }


    public function store(Request $request)
    {
        $name = $request->input('name');
        $crop = $request->input('crop');
        $kod = $request->input('kod');
        $count = DB::table('crops_selection')
            ->where('name', '=', $name)
            ->where('crop_id','=',$crop)
            ->count();
        if ($count == 0) {
            $type = new CropsSelection();
            $type->name = $name;
            $type->crop_id = $crop;
            $type->kod = $kod;
            $type->save();
            return redirect('crops_selection/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('crops_selection/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', User::class);

        CropsSelection::destroy($id);
        return redirect('crops_selection/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $crops = DB::table('crops_name')->get()->toArray();
        return view('crops_selection.edit', [
            'type' => CropsSelection::findOrFail($id),
            'editid' => $id,
            'crops' => $crops
        ]);
    }


    public function update(Request $request, $id)
    {
        $type = CropsSelection::findOrFail($id);
        $type->name = $request->input('name');
        $type->crop_id = $request->input('crop');
        $type->kod = $request->input('kod');
        $type->save();

        return redirect('crops_selection/list')->with('message', 'Successfully Updated');
    }

    public function search_by_name(Request $request)
    {

        $user = auth()->user();
        $ownername = $request->input('search');

        if ($ownername != '') {
            $owners = DB::table('crops_selection')
                ->select('id','name', 'kod');

            $owners = $owners->where(function($query) use($ownername){
                $query->where('name', 'like', '%'.$ownername.'%')
                    ->orWhere('kod', 'like', '%'.$ownername.'%');
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
