import './bootstrap';
import Swal from 'sweetalert2';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';
import listPlugin from '@fullcalendar/list';

document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;

    const availabilitySection = document.getElementById('availability-section');
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const availableResourcesList = document.getElementById('available-resources-list');
    const scheduledResourcesList = document.getElementById('scheduled-resources-list');
    const disponiveisFilterContainer = document.getElementById('disponiveis-filter');
    const agendadosFilterContainer = document.getElementById('agendados-filter');

    const config = {
        availabilityUrl: calendarContainer.dataset.availabilityUrl,
        eventsUrl: '/agendamentos/events',
        baseUrl: calendarContainer.dataset.baseUrl,
        ofertas: JSON.parse(calendarContainer.dataset.ofertas || '[]'),
    };
    let currentSelectedDate = null;
    let debounceTimer;

    let state = {
        disponiveis: { search: '', sort_by: 'nome', order: 'asc' },
        agendados: { search: '', sort_by: 'data_hora_inicio', order: 'asc' }
    };

    const calendar = new Calendar(document.getElementById('calendar'), {
        locale: ptBrLocale,
        plugins: [dayGridPlugin, interactionPlugin, listPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        buttonText: { today: 'Hoje', month: 'Mês', list: 'Agenda' },
        selectable: true,
        aspectRatio: 1.5,
        selectAllow: (info) => info.start >= new Date(new Date().setHours(0, 0, 0, 0)),
        dateClick: (info) => {
            currentSelectedDate = info.date;
            state.disponiveis = { search: '', sort_by: 'nome', order: 'asc' };
            state.agendados = { search: '', sort_by: 'data_hora_inicio', order: 'asc' };
            fetchAvailability(info.date);
        },
        events: config.eventsUrl,
    });
    calendar.render();

    function fetchAvailability(date, pageUrl = null) {
        let url = pageUrl || config.availabilityUrl;
    
        selectedDateDisplay.textContent = date.toLocaleDateString('pt-BR', { dateStyle: 'long' });
        availabilitySection.style.display = 'block';
        if (!pageUrl) { 
            availableResourcesList.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
            scheduledResourcesList.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
        }

        const payload = {
            date: date.toISOString().split('T')[0],
            disponiveis_search: state.disponiveis.search,
            disponiveis_sort_by: state.disponiveis.sort_by,
            disponiveis_order: state.disponiveis.order,
            agendados_search: state.agendados.search,
            agendados_sort_by: state.agendados.sort_by,
            agendados_order: state.agendados.order,
        };

        axios.post(url, payload)
            .then(response => {
                renderFilter(disponiveisFilterContainer, 'disponiveis', state.disponiveis.search);
                renderFilter(agendadosFilterContainer, 'agendados', state.agendados.search);
                
                renderPaginatedTable(availableResourcesList, response.data.disponiveis, renderAvailableResourceRow, date, [
                    { key: 'nome', label: 'Recurso' },
                    { key: 'quantidade', label: 'Qtd' },
                    { key: 'acao', label: 'Ação' }
                ], 'disponiveis');
                
                renderPaginatedTable(scheduledResourcesList, response.data.agendados, renderScheduledResourceRow, date, [
                    { key: 'recurso.nome', label: 'Recurso' },
                    { key: 'data_hora_inicio', label: 'Horário' },
                    { key: 'oferta.turma.serie', label: 'Turma' },
                    { key: 'oferta.professor.nome_completo', label: 'Professor' },
                    { key: 'acao', label: 'Ação' }
                ], 'agendados');
            })
            .catch(() => {
                Swal.fire('Erro!', 'Não foi possível buscar a disponibilidade.', 'error');
            });
    }

    function renderFilter(container, type, value) {
        container.innerHTML = `<input type="text" class="filter-input" data-type="${type}" value="${value}" placeholder="Pesquisar...">`;
    }

    function renderPaginatedTable(container, paginatedData, rowRenderer, date, headers, type) {
        if (!paginatedData || paginatedData.data.length === 0) {
            const message = type === 'disponiveis' ? 'Nenhum recurso disponível.' : 'Nenhum recurso agendado.';
            container.innerHTML = `<p class="placeholder-text">${message}</p>`;
            return;
        }

        const headerHtml = `<thead><tr>${headers.map(h => {
            if (h.key === 'acao') {
                return `<th>${h.label}</th>`;
            }
            return `<th><a href="#" class="sort-link" data-type="${type}" data-sort="${h.key}">${h.label} <i class="fas ${getSortIcon(type, h.key)}"></i></a></th>`;
        }).join('')}</tr></thead>`;

        const bodyHtml = `<tbody>${paginatedData.data.map(item => rowRenderer(item, date)).join('')}</tbody>`;
        const paginationHtml = createPaginationLinks(paginatedData);
        container.innerHTML = `<table class="table">${headerHtml}${bodyHtml}</table>${paginationHtml}`;
    }

    function getSortIcon(type, key) {
        if (state[type].sort_by === key) {
            return state[type].order === 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short';
        }
        return 'fa-sort';
    }

    const renderAvailableResourceRow = (res, date) => `
        <tr>
            <td>${res.nome}</td>
            <td>${res.quantidade}</td>
            <td><button class="btn btn-sm book-btn" data-id="${res.id_recurso}" data-name="${res.nome}" data-date="${date.toISOString().split('T')[0]}">Agendar</button></td>
        </tr>`;

    const renderScheduledResourceRow = (ag) => {
        const turma = ag.oferta?.turma?.serie || 'N/A';
        const professor = ag.oferta?.professor?.nome_completo || 'N/A';
        const horaInicio = ag.data_hora_inicio ? ag.data_hora_inicio.slice(11, 16) : '--:--';
        const horaFim = ag.data_hora_fim ? ag.data_hora_fim.slice(11, 16) : '--:--';
        const cancelButton = ag.can_cancel
            ? `<button class="btn-cancel" data-id="${ag.id_agendamento}" data-name="${ag.recurso.nome}">Desagendar</button>`
            : '';

        return `
        <tr>
            <td>${ag.recurso.nome}</td>
            <td>${horaInicio} - ${horaFim}</td>
            <td>${turma}</td>
            <td>${professor}</td>
            <td>${cancelButton}</td>
        </tr>`;
    };
    
    function createPaginationLinks(data) {
        if (!data || data.links.length <= 3) return '';
        let html = '<nav class="pagination-links"><ul class="pagination pagination-sm">';
        data.links.forEach(link => {
            html += `<li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-url="${link.url}">${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}</a>
                     </li>`;
        });
        html += '</ul></nav>';
        return html;
    }
    
    document.body.addEventListener('input', e => {
        if (e.target.matches('.filter-input')) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const type = e.target.dataset.type;
                state[type].search = e.target.value;
                fetchAvailability(currentSelectedDate);
            }, 500); 
        }
    });

    document.body.addEventListener('click', e => {
        const link = e.target.closest('.page-link');
        const sortLink = e.target.closest('.sort-link');
        
        if (link && link.closest('.pagination-links')) {
            e.preventDefault();
            const url = link.dataset.url;
            if (url && currentSelectedDate) fetchAvailability(currentSelectedDate, url);
        } else if (sortLink) {
            e.preventDefault();
            const type = sortLink.dataset.type;
            const sortBy = sortLink.dataset.sort;
            if (state[type].sort_by === sortBy) {
                state[type].order = state[type].order === 'asc' ? 'desc' : 'asc';
            } else {
                state[type].sort_by = sortBy;
                state[type].order = 'asc';
            }
            fetchAvailability(currentSelectedDate);
        } else if (e.target.classList.contains('book-btn')) {
            openBookingModal(e.target.dataset.id, e.target.dataset.name, e.target.dataset.date);
        } 
        else if (e.target.classList.contains('btn-cancel')) {
            const id = e.target.dataset.id;
            const name = e.target.dataset.name;
            Swal.fire({
                title: 'Atenção! Ação Irreversível',
                html: `Você tem certeza que deseja cancelar o agendamento do recurso: <br><strong>${name}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar agendamento!',
                cancelButtonText: 'Manter Agendamento'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`${config.baseUrl}/${id}`)
                        .then(() => {
                            Swal.fire('Cancelado!', 'O agendamento foi removido com sucesso.', 'success');
                            calendar.refetchEvents(); 
                            fetchAvailability(currentSelectedDate); 
                        }).catch(err => Swal.fire('Erro!', err.response?.data?.message || 'Não foi possível cancelar o agendamento.', 'error'));
                }
            });
        }
    });

    function openBookingModal(resourceId, resourceName, date) {
        let ofertasOptions = config.ofertas.length > 0
            ? config.ofertas.map(o => `<option value="${o.id_oferta}">${o.turma.serie} / ${o.componente_curricular.nome} (${o.professor.nome_completo})</option>`).join('')
            : '<option value="" disabled>Nenhuma turma/disciplina encontrada.</option>';

        Swal.fire({
            title: `Agendar: ${resourceName}`,
            html: `
                <input type="hidden" id="swal_id_recurso" value="${resourceId}">
                <div class="swal-form-group">
                    <label for="swal_id_oferta">Minha Turma/Disciplina</label>
                    <select id="swal_id_oferta" class="form-select">${ofertasOptions}</select>
                </div>
                <div class="swal-form-group">
                    <label for="swal_data_hora_inicio">Início</label>
                    <input type="datetime-local" id="swal_data_hora_inicio" class="form-control">
                </div>
                <div class="swal-form-group">
                    <label for="swal_data_hora_fim">Fim</label>
                    <input type="datetime-local" id="swal_data_hora_fim" class="form-control">
                </div>
            `,
            confirmButtonText: 'Salvar Agendamento',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            didOpen: () => {
                const defaultStartTime = new Date(`${date}T08:00:00`);
                document.getElementById('swal_data_hora_inicio').value = formatToDateTimeLocal(defaultStartTime);
                document.getElementById('swal_data_hora_fim').value = formatToDateTimeLocal(new Date(defaultStartTime.getTime() + 60 * 60 * 1000));
            },
            preConfirm: () => {
                const data = {
                    id_recurso: document.getElementById('swal_id_recurso').value,
                    id_oferta: document.getElementById('swal_id_oferta').value,
                    data_hora_inicio: document.getElementById('swal_data_hora_inicio').value,
                    data_hora_fim: document.getElementById('swal_data_hora_fim').value,
                };
                if (!data.id_oferta || !data.data_hora_inicio || !data.data_hora_fim) {
                    Swal.showValidationMessage('Todos os campos são obrigatórios.');
                    return false;
                }
                return axios.post(config.baseUrl, data)
                    .catch(error => {
                        const msg = error.response?.data?.errors ? Object.values(error.response.data.errors).flat().join('<br>') : (error.response?.data?.message || 'Ocorreu um erro.');
                        Swal.showValidationMessage(`Falha no agendamento: ${msg}`);
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Sucesso!', 'Agendamento salvo!', 'success').then(() => {
                    calendar.refetchEvents();
                    fetchAvailability(currentSelectedDate);
                });
            }
        });
    }
    
    function formatToDateTimeLocal(date) {
        if (!date) return '';
        const tzoffset = (new Date()).getTimezoneOffset() * 60000;
        return (new Date(date - tzoffset)).toISOString().slice(0, 16);
    }
});