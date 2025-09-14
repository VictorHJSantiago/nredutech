@extends('layouts.app')

@section('title', 'Gerenciamento de Turmas')

@section('content')
<div class="main-content">
    <header class="header-section">
        <h1>Gerenciamento de Turmas</h1>
        <p class="subtitle">Cadastre turmas e atribua professores/disciplinas (Ofertas).</p>
    </header>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Opa!</strong> Ocorreram alguns problemas:
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="location-management-grid">

        <div class="add-item-card">
            <h3><i class="fas fa-plus icon-left"></i> Adicionar Nova Turma</h3>
            <form method="POST" action="{{ route('turmas.store') }}">
                @csrf
                
                <div class="form-group">
                    <label for="serie">Série/Nome da Turma</label>
                    <input type="text" id="serie" name="serie" required placeholder="Ex: 9º Ano A" value="{{ old('serie') }}">
                </div>

                <div class="form-group">
                    <label for="ano_letivo">Ano Letivo</label>
                    <input type="number" id="ano_letivo" name="ano_letivo" required placeholder="Ex: {{ date('Y') }}" value="{{ old('ano_letivo', date('Y')) }}" min="2000" max="2100">
                </div>

                <div class="form-group">
                    <label for="turno">Turno</label>
                    <select id="turno" name="turno" required>
                        <option value="manha" @selected(old('turno') == 'manha')>Manhã</option>
                        <option value="tarde" @selected(old('turno') == 'tarde')>Tarde</option>
                        <option value="noite" @selected(old('turno') == 'noite')>Noite</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nivel_escolaridade">Nível de Escolaridade</label>
                    <select id="nivel_escolaridade" name="nivel_escolaridade" required>
                        <option value="fundamental_1" @selected(old('nivel_escolaridade') == 'fundamental_1')>Fundamental I</option>
                        <option value="fundamental_2" @selected(old('nivel_escolaridade') == 'fundamental_2')>Fundamental II</option>
                        <option value="medio" @selected(old('nivel_escolaridade') == 'medio')>Ensino Médio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_escola">Escola</label>
                    <select id="id_escola" name="id_escola" required>
                        <option value="">Selecione a escola</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id_escola }}" @selected(old('id_escola') == $escola->id_escola)>
                                {{ $escola->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 1rem;"><i class="fas fa-plus icon-left"></i> Adicionar Turma</button>
            </form>
        </div>

        <div class="list-item-card full-width">
            <h3><i class="fas fa-list-ul icon-left"></i> Turmas Cadastradas</h3>
            <table class="componentes-table"> 
                <thead>
                    <tr>
                        @php
                            function sort_link($coluna, $titulo, $sortBy, $order) {
                                $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
                                $icon = $sortBy == $coluna 
                                    ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                                    : 'fa-sort';
                                $isActive = $sortBy == $coluna ? 'active' : '';
                                $url = route('turmas.index', array_merge(request()->except(['page']), [
                                    'sort_by' => $coluna,
                                    'order' => $newOrder
                                ]));
                                return "<th><a href=\"$url\" class=\"$isActive\">$titulo <i class=\"fas $icon sort-icon\"></i></a></th>";
                            }
                        @endphp
                        {!! sort_link('serie', 'Turma (Série)', $sortBy, $order) !!}
                        {!! sort_link('turno', 'Turno', $sortBy, $order) !!}
                        {!! sort_link('ano_letivo', 'Ano Letivo', $sortBy, $order) !!}
                        {!! sort_link('escola_nome', 'Escola', $sortBy, $order) !!}
                        <th class="actions-header">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($turmas as $turma)
                        <tr>
                            <td>{{ $turma->serie }}</td>
                            <td>{{ ucfirst($turma->turno) }}</td>
                            <td>{{ $turma->ano_letivo }}</td>
                            <td>{{ $turma->escola->nome ?? 'N/A' }}</td>
                            <td class="actions">
                                <a href="{{ route('turmas.show', $turma->id_turma) }}" class="btn-edit btn-manage-offers" title="Gerenciar Ofertas (Professores/Disciplinas)">
                                    📂 Gerenciar Ofertas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Nenhuma turma encontrada. Cadastre uma no formulário acima.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="pagination-links" style="margin-top: 20px;">
                {{ $turmas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection