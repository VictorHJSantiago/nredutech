@extends('layouts.app')

@section('title', 'Configurações Gerais')

@section('content')
    <header class="header-section">
        <h1>Configurações Gerais</h1>
        <p class="subtitle">Ajuste preferências e parâmetros do sistema</p>
    </header>

    <section class="config-section">
        @include('settings.partials.user-account-section')

        @include('settings.partials.notification-preferences-form', ['preferencias' => $preferencias])

        @include('settings.partials.backup-restore-section')
    </section>
@endsection