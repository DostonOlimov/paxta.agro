<?php

namespace App\Http\Controllers;


use App\Models\Clients;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index()
    {
        $title = 'Urug\' avlodini qo\'shish';
        $crops = DB::table('crops_name')->get()->toArray();
        return view('clients.add', compact('title','crops'));
    }


    public function list()
    {
        $title = 'Uru\'glik avlodlari';
        $types = Clients::with('state')->orderBy('id')->get();
        return view('clients.list', compact('types','title'));
    }


    public function store(Request $request)
    {
        $name = $request->input('name');
        $crop = $request->input('crop');
        $kod = $request->input('kod');
        $count = DB::table('clients')
            ->where('name', '=', $name)
            ->where('crop_id','=',$crop)
            ->count();
        if ($count == 0) {
            $type = new Clients();
            $type->name = $name;
            $type->crop_id = $crop;
            $type->kod = $kod;
            $type->save();
            return redirect('clients/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('clients/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', User::class);

        Clients::destroy($id);
        return redirect('clients/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $crops = DB::table('crops_name')->get()->toArray();
        return view('clients.edit', [
            'type' => Clients::findOrFail($id),
            'editid' => $id,
            'crops' => $crops
        ]);
    }


    public function update(Request $request, $id)
    {
        $type = Clients::findOrFail($id);
        $type->name = $request->input('name');
        $type->crop_id = $request->input('crop');
        $type->kod = $request->input('kod');
        $type->save();

        return redirect('clients/list')->with('message', 'Successfully Updated');
    }

    public function search_by_name(Request $request)
    {

        $user = auth()->user();
        $ownername = $request->input('search');

        if ($ownername != '') {
            $owners = DB::table('clients')
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
