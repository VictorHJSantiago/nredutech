@extends('layouts.app')

@section('title', 'Editar Professor – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Editar Professor</h1>
        <p class="subtitle">Altere os dados do docente selecionado</p>
    </header>

    <section class="form-section">
        <form class="professor-form" method="POST" action="{{ route('professors.update', $professor) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $professor->name) }}" required />
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="birth_date">Data de Nascimento</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $professor->birth_date->format('Y-m-d')) }}" required />
                    @error('birth_date')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $professor->cpf) }}" required />
                    @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" name="rg" value="{{ old('rg', $professor->rg) }}" required />
                    @error('rg')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $professor->email) }}" required />
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $professor->phone) }}" required />
                    @error('phone')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="registration_id">Registro (SIAPE/RCO)</label>
                    <input type="text" id="registration_id" name="registration_id" value="{{ old('registration_id', $professor->registration_id) }}" required />
                    @error('registration_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="degree">Formação</label>
                    <input type="text" id="degree" name="degree" value="{{ old('degree', $professor->degree) }}" required />
                    @error('degree')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="field_of_study">Área de Formação</label>
                    <input type="text" id="field_of_study" name="field_of_study" value="{{ old('field_of_study', $professor->field_of_study) }}" required />
                    @error('field_of_study')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="discipline_id">Vincular Disciplina</label>
                    <select id="discipline_id" name="discipline_id">
                        <option value="">Nenhuma</option>
                        @foreach ($disciplines as $discipline)
                            <option value="{{ $discipline->id }}" {{ old('discipline_id', $professor->discipline_id) == $discipline->id ? 'selected' : '' }}>
                                {{ $discipline->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('discipline_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </section>
@endsection