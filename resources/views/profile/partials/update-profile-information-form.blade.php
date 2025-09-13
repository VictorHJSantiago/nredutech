<section>
    <header class="section-header">
        <h2>
            {{ __('Informações do Perfil') }}
        </h2>

        <p>
            {{ __("Atualize as informações de perfil e o endereço de e-mail da sua conta.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="nome_completo">{{__('Nome Completo')}}</label>
            <input id="nome_completo" name="nome_completo" type="text" value="{{ old('nome_completo', $usuario->nome_completo) }}" required autofocus autocomplete="name" />
             @error('nome_completo')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="username">{{__('Nome de Usuário')}}</label>
            <input id="username" name="username" type="text" value="{{ old('username', $usuario->username) }}" required autocomplete="username" />
            @error('username')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="email">{{__('Email')}}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email" />
            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="telefone">{{__('Telefone')}}</label>
            <input id="telefone" name="telefone" type="text" value="{{ old('telefone', $usuario->telefone) }}" />
            @error('telefone')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>

            @if (session('status') === 'profile-updated')
                <p class="status-message">{{ __('Salvo.') }}</p>
            @endif
        </div>
    </form>
</section>