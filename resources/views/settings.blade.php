@extends('layouts.app')

@section('title', 'Configurações Gerais')

@section('content')
    <header class="header-section">
        <h1>Configurações Gerais</h1>
        <p class="subtitle">Ajuste preferências e parâmetros do sistema</p>
    </header>
    @if(session('download_backup_url'))
        <div id="backupDownloadTrigger" data-url="{{ session('download_backup_url') }}" style="display: none;"></div>
    @endif

    <section class="config-section">
        @include('settings.partials.user-account-section')

        @include('settings.partials.notification-preferences-form')

        @include('settings.partials.backup-restore-section')
    </section>
@endsection

@push('scripts')
    @vite('resources/js/backup-download.js')
@endpush