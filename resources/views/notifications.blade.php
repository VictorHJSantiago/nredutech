@extends('layouts.app')

@section('title', 'Notificações – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Notificações</h1>
        <p class="subtitle">Veja alertas e atualizações do sistema</p>
    </header>

    <section class="notifications-list">
        @forelse ($notifications as $notification)
            <div class="notification-card {{ $notification->unread() ? 'unread' : '' }}">
                <div class="notif-icon">
                    {{ $notification->data['icon'] ?? '📬' }}
                </div>
                <div class="notif-content">
                    <p class="notif-text">{!! $notification->data['text'] !!}</p>
                    <span class="notif-date">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="notification-card">
                <div class="notif-icon">📭</div>
                <div class="notif-content">
                    <p class="notif-text">Você não tem nenhuma notificação nova.</p>
                </div>
            </div>
        @endforelse
    </section>
@endsection