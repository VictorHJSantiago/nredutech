@extends('layouts.app')

@section('title', 'Usuários – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Gestão de Usuários</h1>
        <p class="subtitle">Aprovar, editar ou remover cadastros (Somente Admin)</p>
    </header>

    <div class="form-actions" style="text-align: right; margin: 20px auto; max-width: 1200px;">
        <a href="{{ route('users.create') }}" class="btn-primary">+ Cadastrar Usuário</a>
    </div>

    <section class="filter-bar" style="max-width: 1200px; margin: 20px auto;">
        <form method="GET" action="{{ route('users.index') }}" class="filter-form-inline">
            <input type="text" name="search" placeholder="Buscar por nome ou e-mail..." value="{{ request('search') }}">
            <select name="role">
                <option value="">Todos os papéis</option>
                <option value="professor" {{ request('role') == 'professor' ? 'selected' : '' }}>Professor</option>
                <option value="gestor" {{ request('role') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                <option value="administrador" {{ request('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
            </select>
            <button type="submit" class="btn-search">🔍 Filtrar</button>
        </form>
    </section>

    <section class="table-section" style="max-width: 1200px; margin: auto;">
        <div class="table-wrapper">
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>Usuário</th>
                        <th>E-mail</th>
                        <th>Papel</th>
                        <th>Status</th>
                        <th>CPF</th>
                        <th>Registro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <span class="status-{{ Str::slug($user->status) }}">{{ ucfirst($user->status) }}</span>
                            </td>
                            <td>{{ $user->cpf ?? 'N/A' }}</td>
                            <td>{{ $user->registration_id ?? 'N/A' }}</td>
                            <td class="acao-cell">
                                @if ($user->status == 'pendente')
                                    <form action="{{ route('users.approve', $user) }}" method="POST" class="action-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-approve" title="Aprovar">✅</button>
                                    </form>
                                    <form action="{{ route('users.reject', $user) }}" method="POST" class="action-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-reject" title="Rejeitar">❌</button>
                                    </form>
                                @else
                                    <a href="{{ route('users.edit', $user) }}" class="btn-edit" title="Editar">✏️</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="action-form" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Excluir">🗑️</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection