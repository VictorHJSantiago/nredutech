@extends('layouts.guest')

@section('title', 'Confirmar Ação')

@section('content')
<div class="box confirm-password-box">
  <h2>Confirmar Ação Segura</h2>

  <p class="info-text">
      Para sua segurança, por favor, confirme seu e-mail e senha para continuar.
  </p>

  @if ($errors->any())
    <div class="alert-danger">
        @foreach ($errors->all() as $error)
            <span>{{ $error }}</span><br>
        @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="form-group">
        <label for="email">{{ __('E-mail') }}</label>
        <input id="email" type="email"
               name="email"
               required
               autofocus
               autocomplete="email"
               placeholder="Confirme seu e-mail" 
               value="{{ old('email', auth()->user()->email) }}" />
    </div>

    <div class="form-group">
        <label for="password">{{ __('Senha') }}</label>
        <input id="password" type="password"
               name="password"
               required 
               autocomplete="current-password"
               placeholder="Digite sua senha atual" />
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">
            {{ __('Confirmar') }}
        </button>
    </div>
  </form>
</div>
@endsection