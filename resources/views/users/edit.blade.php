@extends('layouts.app')

@section('title', 'Editar Usuário – NREduTech')

@section('content')
<div class="main-content">
    <header class="header-section">
        <h1>Editar Usuário</h1>
        <p class="subtitle">Altere os dados de: <strong>{{ $usuario->nome_completo }}</strong></p>
    </header>

    <section class="form-section">
        <form class="usuario-form" method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome_completo">Nome Completo</label>
                    <input type="text" id="nome_completo" name="nome_completo" value="{{ old('nome_completo', $usuario->nome_completo) }}" required />
                    @error('nome_completo')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $usuario->username) }}" required />
                    @error('username')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required />
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuário</label>
                    <select id="tipo_usuario" name="tipo_usuario" required>
                        @if(Auth::user()->tipo_usuario === 'administrador')
                        <option value="administrador" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                        @endif
                        <option value="diretor" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'diretor' ? 'selected' : '' }}>Diretor</option>
                        <option value="professor" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'professor' ? 'selected' : '' }}>Professor</option>
                    </select>
                    @error('tipo_usuario')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status_aprovacao">Status</label>
                    <select id="status_aprovacao" name="status_aprovacao" required>
                        <option value="ativo" {{ old('status_aprovacao', $usuario->status_aprovacao) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="pendente" {{ old('status_aprovacao', $usuario->status_aprovacao) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="bloqueado" {{ old('status_aprovacao', $usuario->status_aprovacao) == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                    </select>
                    @error('status_aprovacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="id_escola">Escola/Instituição (Obrigatório para Diretor/Professor)</label>
                    <select id="id_escola" name="id_escola" @if(Auth::user()->tipo_usuario === 'diretor') disabled @endif>
                        <option value="">Nenhuma (Somente Administradores)</option>
                        @foreach($escolas as $escola)
                            <option value="{{ $escola->id_escola }}" {{ old('id_escola', $usuario->id_escola) == $escola->id_escola ? 'selected' : '' }}>
                                {{ $escola->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_escola')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', $usuario->data_nascimento ? $usuario->data_nascimento->format('Y-m-d') : '') }}" required/>
                    @error('data_nascimento')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $usuario->cpf) }}" required />
                    @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" name="rg" value="{{ old('rg', $usuario->rg) }}" required />
                    @error('rg')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label for="rco_siape">Registro (SIAPE ou RCO)</label>
                    <input type="text" id="rco_siape" name="rco_siape" value="{{ old('rco_siape', $usuario->rco_siape) }}" required />
                    @error('rco_siape')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" value="{{ old('telefone', $usuario->telefone) }}" required/>
                    @error('telefone')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="formacao">Formação</label>
                    <input type="text" id="formacao" name="formacao" value="{{ old('formacao', $usuario->formacao) }}" required />
                    @error('formacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="area_formacao">Área de Formação</label>
                    <input type="text" id="area_formacao" name="area_formacao" value="{{ old('area_formacao', $usuario->area_formacao) }}" required />
                    @error('area_formacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('usuarios.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </section>
</div>
@endsection