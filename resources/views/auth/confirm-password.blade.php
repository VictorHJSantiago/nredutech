@extends('layouts.guest')

@section('title', 'Confirmar Senha')

@section('content')
<div class="box confirm-password-box">
  <h2>Confirmar Senha</h2>

  <p class="info-text">
      {{ __('Esta é uma área segura da aplicação. Por favor, confirme a sua senha antes de continuar.') }}
  </p>

  @if ($errors->any())
    <div class="alert-danger">
        @foreach ($errors->all() as $error)
            <span>{{ $error }}</span>
        @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="form-group">
        <label for="password">{{ __('Password') }}</label>
        <input id="password" type="password"
               name="password"
               required autocomplete="current-password"
               placeholder="Digite a sua senha" />
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">
            {{ __('Confirmar') }}
        </button>
    </div>
  </form>
</div>
@endsection
