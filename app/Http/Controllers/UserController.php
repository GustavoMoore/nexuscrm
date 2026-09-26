<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('id')->get()->map(fn (User $user) => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'deactivated_at' => $user->deactivated_at, 'can' => ['deactivate' => $user->role === 'gestor' && $user->id !== $request->user()->id]]);

        return Inertia::render('usuarios/index', ['users' => $users, 'can' => ['create' => true]]);
    }

    public function store(Request $request)
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'lowercase', Rule::unique('users', 'email')], 'password' => ['required', 'string', 'min:8']]);
        $user = new User;
        $user->fill($data);
        $user->forceFill(['role' => 'gestor', 'must_change_password' => true])->save();

        return back()->with('success', 'Gestor criado.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'gestor', 403);
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'lowercase', Rule::unique('users', 'email')->ignore($user->id)]]);
        $user->update($data);

        return back()->with('success', 'Gestor atualizado.');
    }

    public function deactivate(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id || $user->role !== 'gestor', 403);
        $user->forceFill(['deactivated_at' => now(), 'remember_token' => Str::random(60)])->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Gestor desativado.');
    }

    public function activate(User $user)
    {
        abort_unless($user->role === 'gestor', 403);
        $user->forceFill(['deactivated_at' => null])->save();

        return back()->with('success', 'Gestor reativado.');
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_unless($user->role === 'gestor', 403);
        $data = $request->validate(['password' => ['required', 'string', 'min:8']]);
        $user->forceFill(['password' => $data['password'], 'must_change_password' => true, 'remember_token' => Str::random(60)])->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Senha provisória redefinida.');
    }
}
