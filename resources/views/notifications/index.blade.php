@extends('layouts.app')

@section('title', 'Notificações – NREduTech')

@section('content')
    <header class="header-section header-with-action">
        <h1>Minhas Notificações</h1>
        <p class="subtitle">Veja aqui todos os seus alertas e atualizações do sistema.</p>
        @if($notificacoes->isNotEmpty())
            <form action="{{ route('notifications.clearAll') }}" method="POST" class="clear-all-form">
                @csrf
                <button type="submit" class="btn-clear-all">
                    <i class="fas fa-trash-alt"></i> Limpar Todas
                </button>
            </form>
        @endif
    </header>

    @if(session('success'))
        <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 1rem;">
            {{ session('success') }}
        </div>
    @endif

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
                <div class="notif-actions">
                    <form action="{{ route('notifications.destroy', $notificacao->id_notificacao) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete-notification" title="Excluir notificação">
                            &times;
                        </button>
                    </form>
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

    @if($notificacoes->hasPages())
        <div class="pagination-container" style="margin-top: 2rem;">
            {{ $notificacoes->links() }}
        </div>
    @endif
@endsection