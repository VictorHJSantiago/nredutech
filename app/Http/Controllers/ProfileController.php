<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $usuario = Usuario::where('email', $user->email)->first();

        return view('profile.edit', [
            'user' => $user,
            'usuario' => $usuario,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $usuario = Usuario::where('email', $user->email)->first();

        $validatedData = $request->validated();

        $user->fill([
            'name' => $validatedData['nome_completo'],
            'email' => $validatedData['email'],
        ]);


        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($usuario) {
            $usuario->update([
                'nome_completo' => $validatedData['nome_completo'],
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
                'telefone' => $validatedData['telefone'],
            ]);
        }


        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}