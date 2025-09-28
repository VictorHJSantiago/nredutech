import './bootstrap';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';
import listPlugin from '@fullcalendar/list';

document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;

    const calendarEl = document.getElementById('calendar');
    const availabilitySection = document.getElementById('availability-section');
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const availableResourcesList = document.getElementById('available-resources-list');
    const scheduledResourcesList = document.getElementById('scheduled-resources-list');
    
    const config = {
        availabilityUrl: calendarContainer.dataset.availabilityUrl,
        eventsUrl: calendarContainer.dataset.eventsUrl,
        baseUrl: calendarContainer.dataset.baseUrl,
        now: new Date(calendarContainer.dataset.now),
        ofertas: JSON.parse(calendarContainer.dataset.ofertas || '[]'),
    };
    let currentSelectedDate = null;

    const calendar = new Calendar(calendarEl, {
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
            fetchAvailability(info.date);
        },
        events: config.eventsUrl, 
    });
    calendar.render();

    function fetchAvailability(date, pageUrl = null) {
        const url = pageUrl || config.availabilityUrl;
        selectedDateDisplay.textContent = date.toLocaleDateString('pt-BR', { dateStyle: 'long' });
        availabilitySection.style.display = 'block';
        availableResourcesList.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
        scheduledResourcesList.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';

        axios.post(url, { date: date.toISOString().split('T')[0] })
            .then(response => {
                renderPaginatedList(availableResourcesList, response.data.disponiveis, renderAvailableResourceItem, date);
                renderPaginatedList(scheduledResourcesList, response.data.agendados, renderScheduledResourceItem, date);
            })
            .catch(error => {
                Swal.fire('Erro!', 'Não foi possível buscar a disponibilidade.', 'error');
            });
    }

    function renderPaginatedList(container, paginatedData, itemRenderer, date) {
        if (!paginatedData || paginatedData.data.length === 0) {
            const message = container.id.includes('available') ? 'Nenhum recurso disponível.' : 'Nenhum recurso agendado.';
            container.innerHTML = `<p class="placeholder-text">${message}</p>`;
            return;
        }
        let itemsHtml = paginatedData.data.map(item => itemRenderer(item, date)).join('');
        let paginationHtml = createPaginationLinks(paginatedData);
        container.innerHTML = `<ul class="resource-list">${itemsHtml}</ul>${paginationHtml}`;
    }

    const renderAvailableResourceItem = (res, date) => `
        <li>
            <span>${res.nome} (Qtd: ${res.quantidade})</span>
            <button class="btn btn-sm btn-success book-btn" data-id="${res.id_recurso}" data-name="${res.nome}" data-date="${date.toISOString().split('T')[0]}">Agendar</button>
        </li>`;

    const renderScheduledResourceItem = (ag) => {
        const turma = ag.oferta?.turma?.serie || 'N/A';
        const professor = ag.oferta?.professor?.nome_completo.split(' ')[0] || 'N/A';
        return `
        <li class="appointment-list">
            <div><strong>${ag.recurso.nome}</strong></div>
            <div class="details">${ag.data_hora_inicio.slice(11, 16)} - ${ag.data_hora_fim.slice(11, 16)} | Turma: ${turma}</div>
            <div class="details">Prof: ${professor}</div>
        </li>`;
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
    
    document.body.addEventListener('click', e => {
        const link = e.target.closest('.page-link');
        if (link && link.closest('.pagination-links')) {
            e.preventDefault();
            const url = link.dataset.url;
            if (url && currentSelectedDate) fetchAvailability(currentSelectedDate, url);
        }
        if (e.target.classList.contains('book-btn')) {
            openBookingModal(e.target.dataset.id, e.target.dataset.name, e.target.dataset.date);
        }
    });

    function openBookingModal(resourceId, resourceName, date) {
        let ofertasOptions = config.ofertas.length > 0
            ? config.ofertas.map(o => `<option value="${o.id_oferta}">${o.turma.serie} / ${o.componente_curricular.nome}</option>`).join('')
            : '<option value="" disabled>Nenhuma turma/disciplina encontrada para seu usuário.</option>';

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
                Swal.fire('Sucesso!', 'Agendamento salvo!', 'success').then(() => window.location.reload());
            }
        });
    }
    
    function formatToDateTimeLocal(date) {
        if (!date) return '';
        const tzoffset = (new Date()).getTimezoneOffset() * 60000;
        return (new Date(date - tzoffset)).toISOString().slice(0, 16);
    }
});