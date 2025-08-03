@extends('layouts.app')

@section('title', 'Editar Recurso – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Editar Recurso Didático</h1>
        <p class="subtitle">Altere os dados do recurso didático selecionado</p>
    </header>

    <section class="form-section">
        <form class="material-form" method="POST" action="{{ route('resources.update', $resource) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nome do Material</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $resource->name) }}" required />
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="type">Tipo</label>
                    <select id="type" name="type" required>
                        <option value="documento" {{ old('type', $resource->type) == 'documento' ? 'selected' : '' }}>Documento</option>
                        <option value="equipamento" {{ old('type', $resource->type) == 'equipamento' ? 'selected' : '' }}>Equipamento</option>
                        <option value="video" {{ old('type', $resource->type) == 'video' ? 'selected' : '' }}>Vídeo</option>
                        <option value="apresentacao" {{ old('type', $resource->type) == 'apresentacao' ? 'selected' : '' }}>Apresentação</option>
                    </select>
                    @error('type')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="brand">Marca</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand', $resource->brand) }}" />
                    @error('brand')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="serial_number">N.º de Série</label>
                    <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number', $resource->serial_number) }}" />
                    @error('serial_number')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Quantidade</label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $resource->quantity) }}" min="0" required />
                    @error('quantity')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="funcionando" {{ old('status', $resource->status) == 'funcionando' ? 'selected' : '' }}>Funcionando</option>
                        <option value="quebrado" {{ old('status', $resource->status) == 'quebrado' ? 'selected' : '' }}>Quebrado</option>
                        <option value="descartado" {{ old('status', $resource->status) == 'descartado' ? 'selected' : '' }}>Descartado</option>
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="supplier">Fornecedor</label>
                    <input type="text" id="supplier" name="supplier" value="{{ old('supplier', $resource->supplier) }}" />
                    @error('supplier')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="institution_id">Instituição</label>
                    <select id="institution_id" name="institution_id" required>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}" {{ old('institution_id', $resource->institution_id) == $institution->id ? 'selected' : '' }}>
                                {{ $institution->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('institution_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="observations">Observações</label>
                    <textarea id="observations" name="observations" rows="3">{{ old('observations', $resource->observations) }}</textarea>
                    @error('observations')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </section>
@endsection