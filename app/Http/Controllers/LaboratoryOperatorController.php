<?php

namespace App\Http\Controllers;

use App\Models\Laboratories;
use App\Models\LaboratoryOperator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaboratoryOperatorController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        $operators = LaboratoryOperator::with('laboratory');
        if($user->branch_id == User::BRANCH_STATE){
            $operators=$operators->whereHas('laboratory.city.region', function($query) use ($user) {
                $query->where('state_id', $user->state_id);
            });
        }

        $operators=$operators->where('status', 1)->get();
        return view('laboratory_operators.index', compact('operators'));
    }

    public function create()
    {
        $laboratories = Laboratories::all();
        return view('laboratory_operators.create', compact('laboratories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'laboratory_id' => 'required|exists:laboratories,id',
        ]);

        $validated['status']=1;
        LaboratoryOperator::create($validated);
        return redirect()->route('laboratory_operators.index');
    }

    public function edit(LaboratoryOperator $laboratoryOperator)
    {
        $laboratories = Laboratories::all();
        return view('laboratory_operators.edit', compact('laboratoryOperator', 'laboratories'));
    }

    public function update(Request $request, LaboratoryOperator $laboratoryOperator)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'laboratory_id' => 'required|exists:laboratories,id',
        ]);

        $laboratoryOperator->update($validated);
        return redirect()->route('laboratory_operators.index');
    }

    public function destroy(LaboratoryOperator $laboratoryOperator)
    {
        $laboratoryOperator->update(['status' => 0]);
        return redirect()->route('laboratory_operators.index');
    }
}

