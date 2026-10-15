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
     * Valida la richiesta di login dell'utente.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
            $this->username() . '.required' => 'Inserisci il nome utente.',
            'password.required' => 'Inserisci la password.',
        ]);
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

    /**
     * Risposta in caso di credenziali errate.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $message = trans('auth.failed');
        if ($message === 'auth.failed' || empty($message)) {
            $message = 'Nome utente o password errati. Verifica i tuoi dati e riprova.';
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [$message],
        ]);
    }
}
