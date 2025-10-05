<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter; 
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Str; 

class ConfirmablePasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $throttleKey = Str::transliterate('confirm-password|' . $request->user()->id_usuario);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            event(new \Illuminate\Auth\Events\Lockout($request));
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'password' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($request->email !== $request->user()->email) {
            throw ValidationException::withMessages([
                'email' => __('O e-mail fornecido não corresponde ao do usuário autenticado.'),
            ]);
        }

        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            RateLimiter::hit($throttleKey);

            if (RateLimiter::attempts($throttleKey) >= 5) {
                $user = $request->user();
                $user->status_aprovacao = 'bloqueado';
                $user->save();

                RateLimiter::clear($throttleKey); 
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors(['email' => 'Sua conta foi bloqueada por excesso de tentativas de confirmação de senha.']);
            }

            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->put('auth.password_confirmed_at', time());
        
        $intendedUrl = $request->session()->pull('url.intended', route('settings'));

        return redirect($intendedUrl);
    }
}