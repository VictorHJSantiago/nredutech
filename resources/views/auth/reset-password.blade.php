@extends('layouts.guest')

@section('title', 'Criar Nova Senha')

@push('scripts')
    @vite('resources/js/password-toogle.js')
@endpush

@section('content')
<div class="box">
  <h2>Criar Nova Senha</h2>
  <form method="POST" action="{{ route('password.store') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <label for="email">E-mail</label>
    <input
      type="email"
      id="email"
      name="email"
      placeholder="Confirme seu e-mail"
      value="{{ old('email', $request->email) }}"
      required
      autofocus
    />
    @error('email')
        <span class="error-message">{{ $message }}</span>
    @enderror

    <label for="password">Nova Senha</label>
    <div class="password-wrapper">
      <input
        type="password"
        id="password"
        name="password"
        placeholder="Digite sua nova senha"
        required
      />
      <i class="fas fa-eye toggle-password"></i>
    </div>
    @error('password')
        <span class="error-message">{{ $message }}</span>
    @enderror
    
    <label for="password_confirmation">Confirmar Nova Senha</label>
    <div class="password-wrapper">
      <input
        type="password"
        id="password_confirmation"
        name="password_confirmation"
        placeholder="Confirme a nova senha"
        required
      />
      <i class="fas fa-eye toggle-password"></i>
    </div>

    <button type="submit" class="btn">Redefinir Senha</button>
  </form>
</div>
@endsection