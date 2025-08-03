@extends('layouts.app')

@section('title', 'Configurações Gerais')

@section('content')
    <header class="header-section">
        <h1>Configurações Gerais</h1>
        <p class="subtitle">Ajuste preferências e parâmetros do sistema</p>
    </header>

    <section class="config-section">
        {{-- Cada seção de configuração é incluída a partir de um arquivo parcial --}}
        
        @include('settings.partials.update-account-form')
        
        @include('settings.partials.notification-preferences-form')

        @include('settings.partials.theme-settings-form')

        @include('settings.partials.manage-locations-form')

        @include('settings.partials.manage-permissions-form')

        @include('settings.partials.backup-restore-section')

    </section>
@endsection
