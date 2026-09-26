<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    public function edit()
    {
        return Inertia::render('auth/trocar-senha');
    }

    public function update(Request $request)
    {
        $data = $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
        if (Hash::check($data['password'], $request->user()->password)) {
            throw ValidationException::withMessages(['password' => 'Escolha uma senha diferente da atual.']);
        }
        $request->user()->forceFill(['password' => $data['password'], 'must_change_password' => false])->save();

        return redirect('/agenda')->with('success', 'Senha alterada.');
    }
}
