@extends('layouts.app')

@section('title', 'Agendamento de Recursos')

@push('styles')
    @vite('resources/css/appointments.css')
@endpush

@section('content')
<div class="appointments-container">
    <div class="calendar-column">
        <div class="card calendar-card">
            <div class="card-header">
                <h3 class="card-title">📅 Calendário de Agendamentos</h3>
            </div>
            <div class="card-body" id="calendar-container"
                 data-availability-url="{{ route('appointments.availability') }}"
                 data-events-url="{{ route('agendamentos.index') }}"
                 data-base-url="{{ url('agendamentos') }}"
                 data-csrf-token="{{ csrf_token() }}"
                 data-now="{{ $now ?? now()->toIso8601String() }}"
                 data-ofertas='@json($ofertas)'>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="lists-column">
        <div class="list-card">
            <h4>Meus Recursos Agendados</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Recurso</th>
                            <th>Data/Hora</th>
                            <th>Turma</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($meusAgendamentos as $agendamento)
                            <tr>
                                <td>{{ $agendamento->recurso->nome }}<br><small class="text-muted">Qtd: {{ $agendamento->recurso->quantidade }}</small></td>
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora_inicio)->format('d/m H:i') }}</td>
                                <td>{{ $agendamento->oferta->turma->serie ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center placeholder-text">Você não possui agendamentos futuros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($meusAgendamentos->hasPages())
                <div class="pagination-links">
                    {{ $meusAgendamentos->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <div id="availability-section" class="mt-4" style="display: none;">
            <h4 class="availability-title">Disponibilidade para <span id="selected-date-display"></span></h4>
            <div class="availability-grid">
                <div class="list-card">
                    <h5>✔️ Disponíveis</h5>
                    <div id="available-resources-list">
                        <p class="placeholder-text">Selecione uma data para ver os recursos.</p>
                    </div>
                </div>
                <div class="list-card">
                    <h5>❌ Agendados</h5>
                    <div id="scheduled-resources-list">
                         <p class="placeholder-text">Nenhum recurso agendado.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection