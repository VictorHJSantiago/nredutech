<section>
    <header class="section-header">
        <h2>
            {{ __('Informações Adicionais') }}
        </h2>

        <p>
            {{ __("Estes são os dados cadastrados no sistema e não podem ser alterados por aqui.") }}
        </p>
    </header>

    <div class="readonly-info-container">
        <div class="readonly-info-grid">
            <div class="readonly-info-group">
                <label>{{__('Data de Nascimento')}}</label>
                <p>{{ $usuario->data_nascimento ? \Carbon\Carbon::parse($usuario->data_nascimento)->format('d/m/Y') : 'Não informado' }}</p>
            </div>
            <div class="readonly-info-group">
                <label>{{__('CPF')}}</label>
                <p>{{ $usuario->cpf ?? 'Não informado' }}</p>
            </div>
            <div class="readonly-info-group">
                <label>{{__('RG')}}</label>
                <p>{{ $usuario->rg ?? 'Não informado' }}</p>
            </div>
            <div class="readonly-info-group">
                <label>{{__('Registro (SIAPE ou RCO)')}}</label>
                <p>{{ $usuario->rco_siape ?? 'Não informado' }}</p>
            </div>
            <div class="readonly-info-group">
                <label>{{__('Formação')}}</label>
                <p>{{ $usuario->formacao ?? 'Não informado' }}</p>
            </div>
             <div class="readonly-info-group">
                <label>{{__('Área de Formação')}}</label>
                <p>{{ $usuario->area_formacao ?? 'Não informado' }}</p>
            </div>
             <div class="readonly-info-group">
                <label>{{__('Tipo de Usuário')}}</label>
                <p>{{ ucfirst($usuario->tipo_usuario) }}</p>
            </div>
            <div class="readonly-info-group">
                <label>{{__('Escola')}}</label>
                <p>{{ $usuario->escola->nome ?? 'Não aplicável (Administrador)' }}</p>
            </div>
        </div>
    </div>
</section>