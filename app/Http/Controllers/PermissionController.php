<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()->isAdmin(), 403, 'Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');

            return $next($request);
        });
    }

    public function index()
    {
        $title = 'Foydalanuvchi ruxsatlari';
        $permissions = Permission::orderBy('id')->get();
        $users = User::with('permissions:id')
            ->where('role', '!=', 'admin')
            ->orderBy('lastname')->orderBy('name')
            ->get();

        return view('permissions.index', compact('title', 'permissions', 'users'));
    }

    public function update(Request $request)
    {
        $validIds = Permission::pluck('id')->all();
        $input = (array) $request->input('permissions', []); // [user_id => [permission_id, ...]]

        foreach (User::where('role', '!=', 'admin')->get() as $user) {
            $ids = array_values(array_intersect(array_map('intval', (array) ($input[$user->id] ?? [])), $validIds));
            $user->permissions()->sync($ids);
        }

        return redirect('permissions')->with('message', 'Successfully Updated');
    }
}
