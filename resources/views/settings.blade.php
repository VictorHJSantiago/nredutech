@extends('layouts.app')

@section('title', 'Configurações Gerais')

@push('styles')
@endpush

@section('content')
    <header class="header-section">
        <h1>Configurações Gerais</h1>
        <p class="subtitle">Ajuste preferências e parâmetros do sistema</p>
    </header>

    @if (session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 0.25rem; margin-bottom: 1.5rem; max-width: 1200px; margin-inline: auto;">
            {{ session('success') }}
        </div>
    @endif
     @if (session('error'))
        <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.25rem; margin-bottom: 1.5rem; max-width: 1200px; margin-inline: auto;">
            {{ session('error') }}
        </div>
    @endif

    <section class="config-section" style="max-width: 1200px; margin-inline: auto;">
        @include('settings.partials.user-account-section')

        @include('settings.partials.notification-preferences-form', ['preferencias' => $preferencias])

        @include('settings.partials.manage-locations-form', ['municipios' => $municipios, 'escolas' => $escolas])

        @include('settings.partials.manage-permissions-form', ['usuarios' => $usuarios])

        @include('settings.partials.backup-restore-section')
    </section>
@endsection