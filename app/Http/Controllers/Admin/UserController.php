<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || auth()->user()->ruolo !== 'admin') {
                abort(403, 'Accesso riservato agli amministratori');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
            'ruolo' => 'required|in:admin,user',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        Log::create([
            'user_id' => auth()->id(),
            'azione' => 'creazione',
            'target_user_id' => $user->id,
            'dettagli' => 'Creato utente: ' . $user->username,
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Utente creato con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'ruolo' => 'required|in:admin,user',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'string|confirmed';
        }
        $validated = $request->validate($rules);
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }
        $user->update($validated);
        Log::create([
            'user_id' => auth()->id(),
            'azione' => 'modifica',
            'target_user_id' => $user->id,
            'dettagli' => 'Modificato utente: ' . $user->username,
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Utente aggiornato');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $username = $user->username;
        $userId = $user->id;
        $user->delete();
        Log::create([
            'user_id' => auth()->id(),
            'azione' => 'eliminazione',
            'target_user_id' => $userId,
            'dettagli' => 'Eliminato utente: ' . $username,
        ]);
        return redirect()->route('admin.users.index')->with('success', 'Utente eliminato');
    }

    /**
     * Display a listing of the logs.
     */
    public function logs()
    {
        $logs = \App\Models\Log::with(['user', 'targetUser'])->latest()->paginate(50);
        return view('admin.users.logs', compact('logs'));
    }
}
