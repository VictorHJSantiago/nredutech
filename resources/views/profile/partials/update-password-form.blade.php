<section>
    <header class="section-header">
        <h2>
            {{ __('Atualizar Senha') }}
        </h2>

        <p>
            {{ __('Garanta que sua conta use uma senha longa e aleatória para se manter segura.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="current_password">{{__('Senha Atual')}}</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" />
            @error('current_password', 'updatePassword')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="password">{{__('Nova Senha')}}</label>
            <input id="password" name="password" type="password" autocomplete="new-password" />
            @error('password', 'updatePassword')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">{{__('Confirmar Nova Senha')}}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>

            @if (session('status') === 'password-updated')
                <p class="status-message">{{ __('Salvo.') }}</p>
            @endif
        </div>
    </form>
</section>