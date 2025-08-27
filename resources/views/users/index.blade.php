@extends('layouts.app')

@section('title', 'Usuários – NREduTech')

@section('content')
<div class="main-content">
    <header class="header-section">
        <h1>Gestão de Usuários</h1>
        <p class="subtitle">Aprovar, editar ou remover cadastros de usuários do sistema.</p>
    </header>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="form-actions">
        <a href="{{ route('usuarios.create') }}" class="btn-primary">+ Cadastrar Usuário</a>
    </div>

    <section class="filter-bar">
        <form action="{{ route('usuarios.index') }}" method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Buscar por nome ou e-mail..." value="{{ request('search') }}" />
            <select name="status">
                <option value="">Todos os Status</option>
                <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="bloqueado" {{ request('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
            </select>
            <button type="submit" class="btn-search">🔍 Filtrar</button>
        </form>
    </section>

    <section class="table-section">
        <table class="usuarios-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Completo</th>
                    <th>E-mail</th>
                    <th>Data de Registro</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id_usuario }}</td>
                        <td>{{ $usuario->nome_completo }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ \Carbon\Carbon::parse($usuario->data_registro)->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($usuario->tipo_usuario) }}</td>
                        <td>
                            @switch($usuario->status_aprovacao)
                                @case('ativo')
                                    <span class="status-aprovado">Aprovado</span>
                                    @break
                                @case('pendente')
                                    <span class="status-pendente">Pendente</span>
                                    @break
                                @case('bloqueado')
                                    <span class="status-rejeitado">Bloqueado</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="acao-cell">
                            @if ($usuario->status_aprovacao == 'pendente')
                                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_aprovacao" value="ativo">
                                    <button type="submit" class="btn-approve" title="Aprovar Cadastro">✅</button>
                                </form>
                                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_aprovacao" value="bloqueado">
                                    <button type="submit" class="btn-reject" title="Rejeitar Cadastro">❌</button>
                                </form>
                            @else
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-edit" title="Editar Usuário">✏️</a>
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Excluir Usuário">🗑️</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum usuário encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-links">
            {{ $usuarios->links() }}
        </div>
    </section>
</div>
@endsection