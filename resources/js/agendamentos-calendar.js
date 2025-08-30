import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';

document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;

    const calendarEl = document.getElementById('calendar');
    const agendamentoModal = new bootstrap.Modal(document.getElementById('agendamentoModal'));
    const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    const form = document.getElementById('agendamentoForm');
    const modalLabel = document.getElementById('modalLabel');
    const deleteButton = document.getElementById('deleteButton');

    const config = {
        recursos: JSON.parse(calendarContainer.dataset.recursos || '[]'),
        eventsUrl: calendarContainer.dataset.eventsUrl,
        csrfToken: calendarContainer.dataset.csrfToken,
        baseUrl: calendarContainer.dataset.baseUrl,
        now: new Date(calendarContainer.dataset.now),
    };

    const threeHoursFromNow = new Date(config.now.getTime() + 3 * 60 * 60 * 1000);

    const calendar = new Calendar(calendarEl, {
        locale: ptBrLocale,
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: true,
        selectable: true,

        events: {
            url: config.eventsUrl,
            failure: () => Swal.fire('Erro!', 'Houve um erro ao carregar os agendamentos!', 'error'),
            eventDataTransform: (eventData) => ({
                id: eventData.id,
                title: `${eventData.recurso.nome} (${eventData.oferta.turma.serie})`,
                start: eventData.dataHoraInicio,
                end: eventData.dataHoraFim,
                extendedProps: {
                    recurso: eventData.recurso,
                    oferta: eventData.oferta,
                    usuario: eventData.oferta.professor,
                    componente: eventData.oferta.componenteCurricular,
                    turma: eventData.oferta.turma,
                }
            })
        },

        selectAllow: (selectInfo) => selectInfo.start >= threeHoursFromNow,
        eventAllow: (dropInfo, draggedEvent) => draggedEvent.start >= threeHoursFromNow,

        dateClick: function(info) {
            if (info.date < threeHoursFromNow) {
                Swal.fire('Atenção', 'Não é possível criar ou editar agendamentos com menos de 3 horas de antecedência.', 'warning');
                return;
            }
            form.reset();
            modalLabel.textContent = 'Novo Agendamento';
            document.getElementById('agendamento_id').value = '';
            document.getElementById('data_hora_inicio').value = formatToDateTimeLocal(info.date);
            document.getElementById('data_hora_fim').value = formatToDateTimeLocal(new Date(info.date.getTime() + 60 * 60 * 1000));
            deleteButton.style.display = 'none';
            agendamentoModal.show();
        },

        eventClick: function (info) {
            const props = info.event.extendedProps;
            document.getElementById('detailRecurso').textContent = props.recurso.nome;
            document.getElementById('detailUsuario').textContent = props.usuario.nome_completo;
            document.getElementById('detailComponente').textContent = props.componente.nome;
            document.getElementById('detailTurma').textContent = props.turma.serie;
            document.getElementById('detailInicio').textContent = info.event.start.toLocaleString();
            document.getElementById('detailFim').textContent = info.event.end.toLocaleString();

            detailsModal.show();
        },
    });

    calendar.render();

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('agendamento_id').value;
        const data = {
            id_recurso: document.getElementById('id_recurso').value,
            id_oferta: document.getElementById('id_oferta').value,
            data_hora_inicio: document.getElementById('data_hora_inicio').value,
            data_hora_fim: document.getElementById('data_hora_fim').value,
            status: document.getElementById('status').value,
        };
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${config.baseUrl}/${id}` : config.baseUrl;

        fetch(url, {
            method: method,
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken, 'Accept': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(response => response.ok ? response.json() : response.json().then(err => { throw err; }))
        .then(() => {
            agendamentoModal.hide();
            calendar.refetchEvents();
            Swal.fire('Sucesso!', 'Agendamento salvo com sucesso.', 'success');
        })
        .catch(error => {
            const errorMessage = error.errors ? Object.values(error.errors).flat().join('\n') : error.message || 'Ocorreu um erro.';
            Swal.fire('Erro!', errorMessage, 'error');
        });
    });

    deleteButton.addEventListener('click', function () {
        const id = document.getElementById('agendamento_id').value;
        if (!id) return;

        Swal.fire({
            title: 'Você tem certeza?', text: "Esta ação não pode ser revertida!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!', cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${config.baseUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': config.csrfToken, 'Accept': 'application/json'}
                })
                .then(response => {
                    if (response.ok) {
                        agendamentoModal.hide();
                        calendar.refetchEvents();
                        Swal.fire('Excluído!', 'O agendamento foi excluído.', 'success');
                    } else { throw new Error('Falha ao excluir.'); }
                })
                .catch(error => Swal.fire('Erro!', error.message, 'error'));
            }
        });
    });

    function formatToDateTimeLocal(date) {
        if (!date) return '';
        const dt = new Date(date);
        dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
        return dt.toISOString().slice(0, 16);
    }
});