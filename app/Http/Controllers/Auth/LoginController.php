<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        session(['language'=>'uz']);
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        session(['crop'=>1]);

        if ($user->role == User::ROLE_CITY_CHIGIT or $user->role == User::ROLE_STATE_CHIGIT_BOSHLIQ or $user->role == User::ROLE_STATE_CHIGI_XODIM ) {
            return redirect('/sifat-sertificates/list');
        } else {
            return redirect($this->redirectTo);
        }
    }
}
