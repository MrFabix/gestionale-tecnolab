<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function updatePassword(Request $request)
    {
    $request->validate([
        'password' => [
            'required',
            'string',
            'min:6',
            'confirmed'
        ],
    ], [
        'password.required' => 'La password è obbligatoria.',
        'password.string' => 'La password deve essere una stringa.',
        'password.min' => 'La password deve contenere almeno 6 caratteri.',
        'password.confirmed' => 'Le password non coincidono.',
    ]);

        $user = Auth::user();
        $user->password =($request->password);
        $user->save();
        return redirect()->route('profile.show')->with('password_updated', true);
    }
}
