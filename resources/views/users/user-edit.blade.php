@extends('layouts.app')

@section('title', 'Editar Usuário – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Editar Usuário</h1>
        <p class="subtitle">Altere os dados do usuário selecionado</p>
    </header>

    <section class="form-section">
        <form class="usuario-form" method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required />
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required />
                    @error('username')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Nova Senha <span class="small-text">(opcional)</span></label>
                    <input type="password" id="password" name="password" placeholder="Deixe em branco para não alterar" />
                    @error('password')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repita a nova senha" />
                </div>

                <div class="form-group">
                    <label for="role">Permissão (Papel)</label>
                    <select id="role" name="role" required>
                        <option value="professor" {{ old('role', $user->role) == 'professor' ? 'selected' : '' }}>Professor</option>
                        <option value="gestor" {{ old('role', $user->role) == 'gestor' ? 'selected' : '' }}>Gestor</option>
                        <option value="administrador" {{ old('role', $user->role) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('role')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="birth_date">Data de Nascimento</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}" required/>
                    @error('birth_date')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf) }}" required />
                    @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" name="rg" value="{{ old('rg', $user->rg) }}" required />
                    @error('rg')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required/>
                    @error('phone')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                 <div class="form-group">
                    <label for="registration_id">Registro (SIAPE ou RCO)</label>
                    <input type="text" id="registration_id" name="registration_id" value="{{ old('registration_id', $user->registration_id) }}" required />
                    @error('registration_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="degree">Formação</label>
                    <input type="text" id="degree" name="degree" value="{{ old('degree', $user->degree) }}" required />
                    @error('degree')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="field_of_study">Área de Formação</label>
                    <input type="text" id="field_of_study" name="field_of_study" value="{{ old('field_of_study', $user->field_of_study) }}" required />
                    @error('field_of_study')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </section>
@endsection