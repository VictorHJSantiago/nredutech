@extends('layouts.app')

@section('title', 'Cadastrar Recurso – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Cadastrar Recurso Didático</h1>
        <p class="subtitle">Preencha os dados para cadastrar um novo recurso</p>
    </header>

    <section class="form-section">
        <form class="material-form" method="POST" action="{{ route('resources.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nome do Material</label>
                    <input type="text" id="name" name="name" placeholder="Ex: Projetor Multimídia" value="{{ old('name') }}" required />
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="type">Tipo</label>
                    <select id="type" name="type" required>
                        <option value="" disabled selected>Selecione um tipo</option>
                        <option value="documento" {{ old('type') == 'documento' ? 'selected' : '' }}>Documento</option>
                        <option value="equipamento" {{ old('type') == 'equipamento' ? 'selected' : '' }}>Equipamento</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Vídeo</option>
                        <option value="apresentacao" {{ old('type') == 'apresentacao' ? 'selected' : '' }}>Apresentação</option>
                    </select>
                    @error('type')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="brand">Marca</label>
                    <input type="text" id="brand" name="brand" placeholder="Ex: Epson" value="{{ old('brand') }}" />
                    @error('brand')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="serial_number">N.º de Série</label>
                    <input type="text" id="serial_number" name="serial_number" placeholder="Ex: SN12345678" value="{{ old('serial_number') }}" />
                    @error('serial_number')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Quantidade</label>
                    <input type="number" id="quantity" name="quantity" placeholder="Ex: 1" value="{{ old('quantity') }}" min="1" required />
                    @error('quantity')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="funcionando" {{ old('status', 'funcionando') == 'funcionando' ? 'selected' : '' }}>Funcionando</option>
                        <option value="quebrado" {{ old('status') == 'quebrado' ? 'selected' : '' }}>Quebrado</option>
                        <option value="descartado" {{ old('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="supplier">Fornecedor</label>
                    <input type="text" id="supplier" name="supplier" placeholder="Ex: Fornecedor XYZ" value="{{ old('supplier') }}" />
                    @error('supplier')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="institution_id">Instituição</label>
                    <select id="institution_id" name="institution_id" required>
                        <option value="" disabled selected>Selecione uma instituição</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                {{ $institution->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('institution_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="observations">Observações</label>
                    <textarea id="observations" name="observations" rows="3" placeholder="Qualquer detalhe adicional sobre o recurso">{{ old('observations') }}</textarea>
                    @error('observations')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Salvar Cadastro</button>
            </div>
        </form>
    </section>
@endsection