@extends('layouts.app')

@section('title', 'Agendamento de Recursos')

@section('content')
    <div class="container-fluid mt-4">
        <div class="card calendar-card">
            <div class="card-header">
                <h3 class="card-title" style="text-align: center; font-size: 1.5rem; color: #0169b4;">📅 Calendário de Agendamentos</h3>
            </div>
            <div class="card-body"
                id="calendar-container"
                data-recursos='@json($recursos)'
                data-events-url="{{ route('agendamentos.index') }}"
                data-base-url="{{ url('agendamentos') }}"
                data-csrf-token="{{ csrf_token() }}"
                data-now="{{ $now ?? now()->toIso8601String() }}">

                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4" id="resource-availability-container">
        <div class="card calendar-card">
            <div class="card-header">
                <h4 class="card-title" style="font-size: 1.3rem; color: #0169b4;">
                    Disponibilidade para: <span id="selected-date-display" style="font-weight: bold;"></span>
                </h4>
            </div>
            <div class="card-body">
                <div id="resource-list-placeholder" class="row">
                </div>
                <nav aria-label="Resource Pagination">
                    <ul class="pagination justify-content-center mt-4" id="resource-pagination-controls">
                    </ul>
                </nav>

            </div>
        </div>
    </div>

    <div class="lists-container">
                <div class="list-card">
            <h4>Próximos Recursos Reservados</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Início</th>
                            <th>Recurso</th>
                            <th>Professor</th>
                            <th>Turma</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservados as $agendamento)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora_inicio)->format('d/m/Y H:i') }}</td>
                                <td>{{ $agendamento->recurso->nome }}</td>
                                <td>{{ $agendamento->oferta->professor->nome_completo ?? 'N/A' }}</td>
                                <td>{{ $agendamento->oferta->turma->serie ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Nenhum recurso reservado encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-links">
                {{ $reservados->links() }}
            </div>
        </div>

        <div class="list-card">
            <h4>Horários Livres Cadastrados</h4>
             <div class="table-responsive">
                <table class="table">
                     <thead>
                        <tr>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Recurso</th>
                            <th>Cadastrado por</th>
                        </tr>
                    </thead>
                    <tbody>
                         @forelse ($disponiveis as $agendamento)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora_inicio)->format('d/m/Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora_fim)->format('d/m/Y H:i') }}</td>
                                <td>{{ $agendamento->recurso->nome }}</td>
                                <td>{{ $agendamento->oferta->professor->nome_completo ?? 'N/A' }}</td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="4" class="text-center">Nenhum horário livre cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-links">
                 {{ $disponiveis->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="agendamentoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="agendamentoForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Novo Agendamento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="agendamento_id">
                        <input type="hidden" id="id_recurso" name="id_recurso">
                        <div class="mb-3">
                            <label for="id_oferta" class="form-label">Turma/Componente</label>
                            <select class="form-select" id="id_oferta" required>
                                <option value="" disabled selected>Selecione uma opção</option>
                                @forelse($ofertas as $oferta)
                                    <option value="{{ $oferta->id_oferta }}">
                                        {{ optional($oferta->turma)->serie ?? 'N/A' }} / {{ optional($oferta->componenteCurricular)->nome ?? 'N/A' }} (Prof: {{ optional($oferta->professor)->nome_completo ?? 'N/A' }})
                                    </option>
                                @empty
                                    <option value="" disabled>Nenhuma turma/disciplina encontrada para seu usuário.</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="data_hora_inicio" class="form-label">Início</label>
                            <input type="datetime-local" class="form-control" id="data_hora_inicio" required>
                        </div>

                        <div class="mb-3">
                            <label for="data_hora_fim" class="form-label">Fim</label>
                            <input type="datetime-local" class="form-control" id="data_hora_fim" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="agendado">Agendado</option>
                                <option value="livre">Livre</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="deleteButton" style="display:none;">Excluir</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="saveButton">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Detalhes do Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Recurso:</strong> <span id="detailRecurso"></span></p>
                    <p><strong>Usuário:</strong> <span id="detailUsuario"></span></p>
                    <p><strong>Componente:</strong> <span id="detailComponente"></span></p>
                    <p><strong>Turma:</strong> <span id="detailTurma"></span></p>
                    <p><strong>Início:</strong> <span id="detailInicio"></span></p>
                    <p><strong>Fim:</strong> <span id="detailFim"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/agendamentos-calendar.js')
@endpush
