<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/'; // cambia destinazione dopo login

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Campo usato per il login.
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Sovrascrive il login per usare password in chiaro (NON hashate).
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::where('username', $request->username)
            ->where('password', $request->password) // confronto diretto
            ->first();

        if ($user) {
            Auth::login($user, $request->filled('remember'));
            return true;
        }

        return false;
    }
}
