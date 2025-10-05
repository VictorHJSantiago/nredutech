<?php

namespace App\Http\Requests\Auth;

use App\Models\Usuario; 
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = Usuario::where('email', $this->input('email'))->first();

        if (!$user || $user->status_aprovacao === 'bloqueado') {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if (RateLimiter::attempts($this->throttleKey()) >= 5) {
                $user->status_aprovacao = 'bloqueado';
                $user->save();
                RateLimiter::clear($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => 'Sua conta foi bloqueada por excesso de tentativas de login. Contate um administrador.',
                ]);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $authenticatedUser = Auth::user();
        if ($authenticatedUser->status_aprovacao !== 'ativo') {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            $message = $authenticatedUser->status_aprovacao === 'pendente'
                ? 'Seu cadastro ainda está pendente de aprovação.'
                : 'Este usuário está bloqueado ou inativo.';

            throw ValidationException::withMessages(['email' => $message]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        // Nota: os métodos string() e ip() podem ser marcados como erro pelo Intelephense,
        // mas eles funcionam corretamente pois são herdados da classe Request do Laravel.
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}