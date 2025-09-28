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
            <div class="card-body table-responsive-wrapper" id="calendar-container"
                 data-availability-url="{{ route('appointments.availability') }}"
                 data-events-url="{{ route('appointments.events') }}"
                 data-base-url="{{ url('agendamentos') }}"
                 data-ofertas='@json($ofertas)'>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="lists-column">
        <div class="list-card">
            <h4>Meus Recursos Agendados</h4>
            <div class="table-responsive-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{!! sort_link('recurso_nome', 'Recurso', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('data_hora_inicio', 'Data/Hora', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('turma_serie', 'Turma', $sortBy, $order) !!}</th>
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
                            <tr><td colspan="3" class="text-center placeholder-text">Você não possui agendamentos futuros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($meusAgendamentos->hasPages())
                <div class="pagination-links">{{ $meusAgendamentos->withQueryString()->links() }}</div>
            @endif
        </div>

        <div id="availability-section" class="mt-4" style="display: none;">
            <h4 class="availability-title">Disponibilidade para <span id="selected-date-display"></span></h4>
            <div class="availability-grid">
                <div class="list-card">
                    <h5>✔️ Disponíveis</h5>
                    <div class="filter-container" id="disponiveis-filter"></div>
                    <div id="available-resources-list" class="table-responsive-wrapper"></div>
                </div>
                <div class="list-card">
                    <h5>❌ Agendados</h5>
                    <div class="filter-container" id="agendados-filter"></div>
                    <div id="scheduled-resources-list" class="table-responsive-wrapper"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    function sort_link($coluna, $titulo, $sortBy, $order) {
        $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
        $icon = $sortBy == $coluna ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short') : 'fa-sort';
        $isActive = $sortBy == $coluna ? 'active' : '';
        $urlParams = array_merge(request()->except('sort_by', 'order'), ['sort_by' => $coluna, 'order' => $newOrder]);
        return '<a href="?' . http_build_query($urlParams) . '" class="' . $isActive . '">' . $titulo . ' <i class="fas ' . $icon . ' sort-icon"></i></a>';
    }
@endphp