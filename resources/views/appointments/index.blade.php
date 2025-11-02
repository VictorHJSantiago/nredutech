@extends('layouts.app')

@section('title', 'Agendamento de Recursos')
@php
    function sort_link($coluna, $titulo, $sortBy, $order) {
        $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
        $icon = $sortBy == $coluna ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short') : 'fa-sort';
        $isActive = $sortBy == $coluna ? 'active' : '';
        $urlParams = array_merge(request()->except('sort_by', 'order'), ['sort_by' => $coluna, 'order' => $newOrder]);
        return '<a href="?' . http_build_query($urlParams) . '" class="' . $isActive . '">' . $titulo . ' <i class="fas ' . $icon . ' sort-icon"></i></a>';
    }
@endphp

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
                 data-ofertas='@json($ofertasJson)'>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="lists-column">
        <div class="list-card">
            <h4>Meus Recursos Agendados</h4>
            
            <div class="search-bar-container mb-3">
                <form action="{{ route('agendamentos.index') }}" method="GET">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="form-control" placeholder="Recurso, Qtd, Prof., Escola, Data..." value="{{ $search ?? '' }}">
                    </div>
                    
                    @if(request('search'))
                        <a href="{{ route('agendamentos.index', request()->except('search', 'page')) }}" class="btn btn-outline-secondary" title="Limpar busca">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif

                    @if(request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if(request('order'))
                        <input type="hidden" name="order" value="{{ request('order') }}">
                    @endif
                </form>
            </div>
            
            <div class="table-responsive-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{!! sort_link('recurso_nome', 'Recurso', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('recurso_quantidade', 'Qtd', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('data_hora_inicio', 'Data/Hora', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('turma_serie', 'Turma', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('professor_nome', 'Professor', $sortBy, $order) !!}</th>
                            <th>{!! sort_link('escola_nome', 'Escola', $sortBy, $order) !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($meusAgendamentos as $agendamento)
                            <tr>
                                <td>{{ $agendamento->recurso->nome }}</td>
                                <td>{{ $agendamento->recurso->quantidade }}</td>
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora_inicio)->format('d/m H:i') }}</td>
                                <td>{{ $agendamento->oferta->turma->serie ?? 'N/A' }}</td>
                                <td>{{ $agendamento->oferta->professor->nome_completo ?? 'N/A' }}</td>
                                <td>{{ $agendamento->oferta->turma->escola->nome ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center placeholder-text">
                                    @if($search)
                                        Nenhum agendamento futuro encontrado para "{{ $search }}".
                                    @else
                                        Você não possui agendamentos futuros.
                                    @endif
                                </td>
                            </tr>
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
                    <div class="filter-container" id="disponiveis-filter">
                    </div>
                    <div id="available-resources-list" class="table-responsive-wrapper"></div>
                </div>
                <div class="list-card">
                    <h5>❌ Agendados</h5>
                    <div class="filter-container" id="agendados-filter">
                    </div>
                    <div id="scheduled-resources-list" class="table-responsive-wrapper"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection