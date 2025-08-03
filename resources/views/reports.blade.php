@extends('layouts.app')

@section('title', 'Relatórios – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Relatórios Personalizados</h1>
        <p class="subtitle">
            Gere relatórios por filtros como município, nível de ensino e instituição
        </p>
    </header>

    <section class="filter-form">
        <form action="{{ route('reports.generate') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="municipality">Município:</label>
                <select id="municipality" name="municipality">
                    <option value="">Todos</option>
                    @foreach ($municipalities as $municipality)
                        <option value="{{ $municipality->id }}" {{ old('municipality') == $municipality->id ? 'selected' : '' }}>
                            {{ $municipality->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="institution">Instituição:</label>
                <select id="institution" name="institution">
                    <option value="">Todas</option>
                    @foreach ($institutions as $institution)
                        <option value="{{ $institution->id }}" {{ old('institution') == $institution->id ? 'selected' : '' }}>
                            {{ $institution->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="education_level">Nível de Escolaridade:</label>
                <select id="education_level" name="education_level">
                    <option value="">Todos</option>
                    <option value="fundamental2" {{ old('education_level') == 'fundamental2' ? 'selected' : '' }}>Fundamental II</option>
                    <option value="medio" {{ old('education_level') == 'medio' ? 'selected' : '' }}>Ensino Médio</option>
                </select>
            </div>

            <div class="form-group">
                <label for="class_type">Tipo de Turma:</label>
                <select id="class_type" name="class_type">
                    <option value="">Todas</option>
                    <option value="manha" {{ old('class_type') == 'manha' ? 'selected' : '' }}>Manhã</option>
                    <option value="tarde" {{ old('class_type') == 'tarde' ? 'selected' : '' }}>Tarde</option>
                    <option value="noite" {{ old('class_type') == 'noite' ? 'selected' : '' }}>Noite</option>
                </select>
            </div>

            <div class="form-group">
                <label for="data_type">Tipo de Dado:</label>
                <select id="data_type" name="data_type">
                    <option value="geral" {{ old('data_type') == 'geral' ? 'selected' : '' }}>Visão Geral</option>
                    <option value="componentes" {{ old('data_type') == 'componentes' ? 'selected' : '' }}>Componentes Curriculares</option>
                    <option value="recursos" {{ old('data_type') == 'recursos' ? 'selected' : '' }}>Recursos Didáticos</option>
                    <option value="usuarios" {{ old('data_type') == 'usuarios' ? 'selected' : '' }}>Usuários Cadastrados</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-gerar">📄 Gerar Relatório</button>
            </div>
        </form>
    </section>

    <section class="relatorio-preview">
        @isset($reportData)
            @include('reports.partials.preview', ['data' => $reportData])
        @else
            <p><strong>A pré-visualização do relatório aparecerá aqui após a geração.</strong></p>
        @endisset
    </section>
@endsection