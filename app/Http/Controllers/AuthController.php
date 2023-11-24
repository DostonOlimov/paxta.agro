<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request,$user)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('/');
        }

        return back()->withErrors([
            'user_name' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function register()
    {
        return view('auth.register');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $credentials =  $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255',],
            'email' => ['required', 'string','email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:4','max:25'],//Rules\Password::defaults()
        ],[
         'email.required' => 'Email kiritilishi kerak.',
        'email.email' => 'Email noto\'g\'ri shaklda kiritilgan.',
        'email.unique' => 'Ushbu email bilan oldin ro\'yxatdan o\'tilgan.',
        'password.required' => 'Parol kiritilishi kerak.',
        'password.min' => 'Parol  :min ta belgidan ko\'p bo\'lishi kerak.',
    ]);
        $user = new User();
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->role = User::ROLE_CUSTOMER;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->status = 1;
        $user->save();
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('/application/my-applications');
        }

        return back()->withErrors([
            'email' => 'Ro\'yxatdan o\'tishda xatolik yuz berdi.',
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // Other validation rules for your fields
        ]);
    }
}
