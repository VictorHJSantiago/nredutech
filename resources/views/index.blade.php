@extends('layouts.app')

@section('title', 'Painel Principal')

@section('content')
<header class="header-section">
    <h1 class="animated-title">Bem‐vindo ao <span>NREduTech</span></h1>
    <p class="subtitle">
        Sistema Web para Gestão de Componentes Curriculares e Recursos Didáticos
    </p>
</header>

<section class="cards-container">
    <div class="card">
        <div class="card-icon">🗂️</div>
        <h3>Componentes</h3>
        <p>Gerencie disciplinas, cargas horárias e materiais de forma integrada.</p>
    </div>
    <div class="card">
        <div class="card-icon">👩‍🏫</div>
        <h3>Professores</h3>
        <p>Cadastre e controle perfis de docentes, permissões e funções.</p>
    </div>
    <div class="card">
        <div class="card-icon">📚</div>
        <h3>Recursos</h3>
        <p>Organize livros, apostilas, vídeos e demais materiais didáticos.</p>
    </div>
    <div class="card">
        <div class="card-icon">📊</div>
        <h3>Relatórios</h3>
        <p>Visualize estatísticas de uso, desempenho e indicadores pedagógicos.</p>
    </div>
</section>
@endsection