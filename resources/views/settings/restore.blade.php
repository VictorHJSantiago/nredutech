@extends('layouts.app')

@section('title', 'Restaurar Backup')

@section('content')
    <header class="header-section">
        <h1>Restaurar Backup do Banco de Dados</h1>
        <p class="subtitle">Sua identidade foi confirmada. Por favor, selecione o arquivo de backup para continuar.</p>
    </header>

    <section class="restore-section">
        @if(session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <strong style="font-size: 1rem;">Ocorreram erros:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.backup.restore-upload') }}" enctype="multipart/form-data" id="restore-form" class="restore-form">
            @csrf
            
            <p class="help-text">
                Envie um arquivo <code>.sql</code> para restaurar o banco de dados.
                <br>
                <strong>Nota:</strong> Se você baixou um backup <code>.zip</code>, você deve <strong>extrair o arquivo <code>.sql</code></strong> de dentro dele primeiro.
            </p>

            <div class="form-group">
                <label for="backup_file" class="form-label">Arquivo de Backup (.sql)</label>
                <div class="file-input-wrapper">
                    <input type="file" name="backup_file" id="backup_file" class="file-input" required accept=".sql,.txt,application/sql,text/plain">
                    <span class="file-input-button"><i class="fas fa-upload icon-left"></i> Escolher arquivo</span>
                    <span class="file-input-text" id="file-chosen-text">Nenhum arquivo escolhido</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="confirm-restore-button" class="btn btn-danger">
                    <i class="fas fa-database"></i> Confirmar Restauração
                </button>
                <a href="{{ route('settings') }}" class="btn-secondary-custom">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </section>
@endsection

