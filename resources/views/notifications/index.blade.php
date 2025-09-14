@extends('layouts.app')

@section('title', 'Notificações – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Minhas Notificações</h1>
        <p class="subtitle">Veja aqui todos os seus alertas e atualizações do sistema.</p>
    </header>

    <section class="notifications-list">
        @forelse ($notificacoes as $notificacao)
            <div class="notification-card {{ $notificacao->created_at->gt(now()->subDay()) ? 'recent' : '' }}">
                <div class="notif-icon">
                    🔔
                </div>
                <div class="notif-content">
                    <h4 class="notif-title">{{ $notificacao->titulo }}</h4>
                    <p class="notif-text">{!! $notificacao->mensagem !!}</p>
                    <span class="notif-date" title="{{ $notificacao->data_envio }}">
                        {{ \Carbon\Carbon::parse($notificacao->data_envio)->diffForHumans() }}
                    </span>
                </div>
            </div>
        @empty
            <div class="notification-card">
                <div class="notif-icon">📭</div>
                <div class="notif-content">
                    <p class="notif-text">Você não tem nenhuma notificação no momento.</p>
                </div>
            </div>
        @endforelse
    </section>

    <div class="pagination-container" style="margin-top: 2rem;">
        {{ $notificacoes->links() }}
    </div>
@endsection