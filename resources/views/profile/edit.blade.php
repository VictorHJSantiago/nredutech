@extends('layouts.app')

@section('title', 'Perfil do Usuário')

@section('content')
<div class="main-content profile-page-container">
    <header class="header-section">
        <h1>Perfil do Usuário</h1>
        <p class="subtitle">Gerencie suas informações pessoais, de acesso e segurança.</p>
    </header>

    <div class="profile-content-wrapper">
        <div class="profile-sections-wrapper">
            
            <div class="profile-section-card">
                <div class="profile-section-inner">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            
            <div class="profile-section-card">
                <div class="profile-section-inner">
                    @include('profile.partials.show-profile-information-form')
                </div>
            </div>

            <div class="profile-section-card">
                <div class="profile-section-inner">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection