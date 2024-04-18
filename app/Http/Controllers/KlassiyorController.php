<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClampData;
use App\Models\Klassiyor;
use App\Models\Laboratories;
use App\Models\LaboratoryProtocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KlassiyorController extends Controller
{

    public function index()
    {
        $user = Auth::user(); // Changed to lowercase 'user'

        // Eager loading 'city' and 'region' relationships
        $laboratories = Laboratories::with('city.region');

        // Check if user is a state employee and filter laboratories accordingly
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
            $userStateId = $user->state_id;

            // Utilizing whereHas for nested relationship querying
            $laboratories->whereHas('city.region', function ($query) use ($userStateId) {
                $query->where('state_id', $userStateId);
            });
        }

        // Fetch the laboratories
        $laboratories = $laboratories->get();

        return view('klassiyor.add', compact('laboratories'));
    }

    public function list()
    {
        $user = Auth::user(); // Changed to lowercase 'user'

        // Eager loading 'laboratory' relationship
        $klassiyor = Klassiyor::with('laboratory');

        // Check if user is a state employee and filter klassiyor accordingly
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
            $userStateId = $user->state_id;

            // Utilizing whereHas for nested relationship querying
            $klassiyor->whereHas('laboratory.city.region', function ($query) use ($userStateId) {
                $query->where('state_id', $userStateId);
            });
        }

        $klassiyors = $klassiyor->orderBy('id', 'desc')->paginate(50);

        return view('klassiyor.list', compact('klassiyors'));
    }

    // klassiyor store
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Extracting inputs from the request
        $data = $request->only(['name', 'laboratory_id', 'kode']);

        // Check if kode already exists
        $count = Klassiyor::where('kode', $data['kode'])->count();

        if ($count == 0) {
            // Create a new Klassiyor instance
            $klassiyor = new Klassiyor();
            $klassiyor->fill($data);
            $klassiyor->save();

            // Redirect with success message
            return redirect('klassiyor/list')->with('message', 'Successfully Submitted');
        } else {
            // Redirect with duplicate data message
            return redirect('klassiyor/add')->with('message', 'Duplicate Data');
        }
    }

    public function destory($id)
    {
        $this->authorize('delete', User::class);

        // Check if there are related ClampData records
        $related = LaboratoryProtocol::where('klassiyor_id', $id)->first();

        if ($related) {
            return redirect('klassiyor/list')->with('message', 'Cannot Deleted');
        }

        // Delete the Klassiyor instance
        Klassiyor::destroy($id);

        return redirect('klassiyor/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        $user = Auth::user(); // Changed to lowercase 'user'

        // Eager loading 'city' and 'region' relationships
        $laboratories = Laboratories::with('city.region');

        // Check if user is a state employee and filter laboratories accordingly
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
            $userStateId = $user->state_id;

            // Utilizing whereHas for nested relationship querying
            $laboratories->whereHas('city.region', function ($query) use ($userStateId) {
                $query->where('state_id', $userStateId);
            });
        }

        // Fetch the laboratories
        $laboratories = $laboratories->get();

        return view('klassiyor.edit', [
            'klassiyor' => Klassiyor::findOrFail($id),
            'laboratories' => $laboratories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', User::class);

        // Find the Klassiyor instance by ID
        $klassiyor = Klassiyor::findOrFail($id);

        // Update the attributes
        $klassiyor->name = $request->input('name');
        $klassiyor->kode = $request->input('kode');
        $klassiyor->laboratory_id = $request->input('laboratory_id');
        $klassiyor->save();

        // Redirect with success message
        return redirect('klassiyor/list')->with('message', 'Successfully Updated');
    }
}
