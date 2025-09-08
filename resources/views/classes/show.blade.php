@extends('layouts.app')

@section('title', 'Gerenciar Turma: ' . $turma->serie)

@section('content')
<div class="main-content">

    <header class="header-section">
        <h1>Gerenciar Turma: {{ $turma->serie }}</h1>
        <p class="subtitle">Escola: <strong>{{ $turma->escola->nome }}</strong> | Turno: <strong>{{ ucfirst($turma->turno) }}</strong> | Ano: <strong>{{ $turma->ano_letivo }}</strong></p>
    </header>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
         <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
     @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ocorreram erros:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-content-schools" style="gap: 1rem;"> 
        
        <div class="card">
            <div class="card-header">
                <h3>Adicionar Professor/Disciplina para esta Turma</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('ofertas.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_turma" value="{{ $turma->id_turma }}">

                    <div class="form-group">
                        <label for="id_componente">Disciplina (Componente Curricular)</label>
                        <select id="id_componente" name="id_componente" class="form-control" required>
                            <option value="" disabled selected>Selecione a disciplina</option>
                            @foreach($componentes as $componente)
                                <option value="{{ $componente->id_componente }}">{{ $componente->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_professor">Professor (Usuário)</label>
                        <select id="id_professor" name="id_professor" class="form-control" required>
                             <option value="" disabled selected>Selecione o professor responsável</option>
                             @foreach($professores as $professor)
                                <option value="{{ $professor->id_usuario }}">{{ $professor->nome_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="button button-primary" style="margin-top: 1rem;">+ Adicionar Oferta</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Ofertas Atuais para: {{ $turma->serie }}</h3>
            </div>
            <div class="card-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Disciplina (Componente)</th>
                            <th>Professor Responsável</th>
                            <th class="actions-header">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($turma->ofertasComponentes as $oferta)
                            <tr>
                                <td>{{ $oferta->componenteCurricular->nome ?? 'Disciplina Excluída' }}</td>
                                <td>{{ $oferta->professor->nome_completo ?? 'Professor Excluído' }}</td>
                                <td class="actions">
                                    <form action="{{ route('ofertas.destroy', $oferta->id_oferta) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta oferta?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-icon btn-delete" title="Excluir Oferta">🗑️ Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Nenhum professor ou disciplina foi atribuído a esta turma ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 1rem;">
            <a href="{{ route('turmas.index') }}" class="btn-secondary">Voltar para Lista de Turmas</a>
        </div>

    </div>
</div>
@endsection