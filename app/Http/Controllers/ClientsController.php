<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user->crop_branch != User::CROP_BRANCH_CHIGIT && $user->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $states = DB::table('tbl_states')->get();
        return view('clients.add', compact('states'));
    }

    public function list()
    {
        $clients = Clients::with('state')->orderBy('name')->get();
        return view('clients.list', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kod'  => 'required|digits:9',
        ]);

        $exists = DB::table('clients')
            ->where('kod', $request->input('kod'))
            ->exists();

        if ($exists) {
            return redirect('clients/add')->with('message', 'Duplicate Data');
        }

        $client = new Clients();
        $client->name     = $request->input('name');
        $client->kod      = $request->input('kod');
        $client->state_id = $request->input('state_id');
        $client->tipp     = 1;
        $client->save();

        return redirect('clients/list')->with('message', 'Successfully Submitted');
    }

    public function destory($id)
    {
        Clients::destroy($id);
        return redirect('clients/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $states = DB::table('tbl_states')->orderBy('name')->get();
        $client = Clients::findOrFail($id);
        return view('clients.edit', compact('client', 'states'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kod'  => 'required|digits:9',
        ]);

        $client = Clients::findOrFail($id);
        $client->name     = $request->input('name');
        $client->kod      = $request->input('kod');
        $client->state_id = $request->input('state_id');
        $client->tipp     = 1;
        $client->save();

        return redirect('clients/list')->with('message', 'Successfully Updated');
    }

    public function search_by_name(Request $request)
    {
        $search = $request->input('search', '');
        if ($search === '') {
            echo 'Nothing to show';
            return;
        }

        $results = DB::table('clients')
            ->select('id', 'name', 'kod')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('kod', 'like', '%' . $search . '%');
            })
            ->limit(15)
            ->get();

        echo $results->isNotEmpty() ? json_encode($results) : 'Nothing to show';
    }
}
