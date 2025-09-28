import './bootstrap'; 
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';

document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;

    const calendarEl = document.getElementById('calendar');
    const agendamentoModalElement = document.getElementById('agendamentoModal');
    const agendamentoModal = new bootstrap.Modal(agendamentoModalElement);
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

    const tenMinutesFromNow = new Date(config.now.getTime() + 10 * 60 * 1000);

    let selectedDate = null;
    let currentEventsOnDay = [];
    let currentPage = 1;
    const RESOURCES_PER_PAGE = 5;

    let eventsByResourceId = new Map(); 

    const availabilityContainer = document.getElementById('resource-availability-container');
    const listPlaceholder = document.getElementById('resource-list-placeholder');
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const paginationControlsContainer = document.getElementById('resource-pagination-controls');

    const calendar = new Calendar(calendarEl, {
        locale: ptBrLocale,
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
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
                    componente: eventData.oferta.componente, 
                    turma: eventData.oferta.turma,
                }
            })
        },

        selectAllow: (selectInfo) => selectInfo.start >= tenMinutesFromNow,
        eventAllow: (dropInfo, draggedEvent) => draggedEvent.start >= tenMinutesFromNow,

        dateClick: function(info) {
            if (info.date < tenMinutesFromNow) {
                Swal.fire('Atenção', 'Não é possível agendar com menos de 10 minutos de antecedência.', 'warning');
                return;
            }
            selectedDate = info.date;

            listPlaceholder.innerHTML = '<div class="d-flex justify-content-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
            paginationControlsContainer.innerHTML = ''; 
            selectedDateDisplay.textContent = 'Carregando...'; 
            availabilityContainer.style.display = 'block';
            
            setTimeout(() => {
                const dayStart = new Date(new Date(info.date).setHours(0, 0, 0, 0));
                const dayEnd = new Date(new Date(info.date).setHours(23, 59, 59, 999));

                currentEventsOnDay = calendar.getEvents().filter(event => {
                    if (!event.start || !event.end) return false; 
                    return event.start < dayEnd && event.end > dayStart;
                });

                eventsByResourceId.clear(); 
                currentEventsOnDay.forEach(event => {
                    const resourceId = event.extendedProps.recurso.id; 
                    if (!eventsByResourceId.has(resourceId)) {
                        eventsByResourceId.set(resourceId, []);
                    }
                    eventsByResourceId.get(resourceId).push(event);
                });
                eventsByResourceId.forEach(eventsArray => {
                    eventsArray.sort((a, b) => new Date(a.start) - new Date(b.start));
                });
                currentPage = 1;
                updateAvailabilityView(info.date);
                availabilityContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

            }, 0); 
        },

        eventClick: function (info) {
            const props = info.event.extendedProps;
            document.getElementById('detailRecurso').textContent = props.recurso.nome;
            document.getElementById('detailUsuario').textContent = props.usuario.nomeCompleto;
            document.getElementById('detailComponente').textContent = props.componente.nome;
            document.getElementById('detailTurma').textContent = props.turma.serie;
            document.getElementById('detailInicio').textContent = new Date(info.event.start).toLocaleString();
            document.getElementById('detailFim').textContent = new Date(info.event.end).toLocaleString();
            detailsModal.show();
        },
    });

    calendar.render();

    function updateAvailabilityView(date) {
        selectedDateDisplay.textContent = date.toLocaleDateString('pt-BR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        renderResourceListSlice(config.recursos, eventsByResourceId, currentPage);
        renderPagination(config.recursos.length, currentPage);
    }

    function renderResourceListSlice(allResources, eventsMap, page) {
        const htmlFragments = [];
        const startIndex = (page - 1) * RESOURCES_PER_PAGE;
        const endIndex = page * RESOURCES_PER_PAGE;
        const paginatedResources = allResources.slice(startIndex, endIndex);

        paginatedResources.forEach(resource => {            
            const resourceBookings = eventsMap.get(resource.id_recurso) || [];
            let bookingsHtml = '';
            if (resourceBookings.length > 0) {
                bookingsHtml = '<ul class="bookings-list">';
                resourceBookings.forEach(booking => {
                    const startTime = new Date(booking.start).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    const endTime = new Date(booking.end).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    const professor = booking.extendedProps.usuario.nomeCompleto.split(' ')[0];
                    bookingsHtml += `<li><strong>${startTime} às ${endTime}:</strong> Reservado (${professor})</li>`;
                });
                bookingsHtml += '</ul>';
            } else {
                bookingsHtml = '<p class="no-bookings">✔️ Nenhum agendamento para este dia.</p>';
            }

            const cardHtml = `
                <div class="col-md-6 col-lg-4">
                    <div class="resource-card">
                        <h5>${resource.nome}</h5>
                        <hr class="my-2">
                        ${bookingsHtml}
                        <button class="btn btn-primary btn-sm book-resource-btn mt-2" 
                                data-resource-id="${resource.id_recurso}" 
                                data-resource-name="${resource.nome}">
                            Agendar este Recurso
                        </button>
                    </div>
                </div>
            `;
            htmlFragments.push(cardHtml);
        });

        listPlaceholder.innerHTML = htmlFragments.join('');
    }

    function renderPagination(totalResources, activePage) {
        const totalPages = Math.ceil(totalResources / RESOURCES_PER_PAGE);
        const htmlFragments = [];

        if (totalPages <= 1) {
            paginationControlsContainer.innerHTML = ''; 
            return; 
        }

        let prevDisabled = (activePage === 1) ? 'disabled' : '';
        htmlFragments.push(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${activePage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            let activeClass = (i === activePage) ? 'active' : '';
            htmlFragments.push(`
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        let nextDisabled = (activePage === totalPages) ? 'disabled' : '';
        htmlFragments.push(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${activePage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
        
        paginationControlsContainer.innerHTML = htmlFragments.join(''); 
    }

    availabilityContainer.addEventListener('click', function(e) {
        const bookBtn = e.target.closest('.book-resource-btn');
        if (bookBtn) {
            e.preventDefault();
            const resourceId = bookBtn.dataset.resourceId; 
            const resourceName = bookBtn.dataset.resourceName;
            openBookingModalForResource(resourceId, resourceName, selectedDate);
            return;
        }

        const pageLink = e.target.closest('.page-link');
        if (pageLink && paginationControlsContainer.contains(pageLink)) {
            e.preventDefault();
            if (pageLink.parentElement.classList.contains('disabled') || pageLink.parentElement.classList.contains('active')) {
                return;
            }
            const page = parseInt(pageLink.dataset.page, 10);
            if (!isNaN(page)) {
                listPlaceholder.innerHTML = '<div class="d-flex justify-content-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
                
                setTimeout(() => {
                    currentPage = page;
                    updateAvailabilityView(selectedDate);
                }, 0);
            }
        }
    });

    function openBookingModalForResource(resourceId, resourceName, date) {
        form.reset();
        modalLabel.textContent = `Agendar: ${resourceName}`;
        document.getElementById('agendamento_id').value = '';
        deleteButton.style.display = 'none';
        const defaultStartTime = new Date(date);
        defaultStartTime.setHours(8, 0, 0, 0);
        const defaultEndTime = new Date(defaultStartTime.getTime() + 60 * 60 * 1000);
        document.getElementById('data_hora_inicio').value = formatToDateTimeLocal(defaultStartTime);
        document.getElementById('data_hora_fim').value = formatToDateTimeLocal(defaultEndTime);
        document.getElementById('id_recurso').value = resourceId; 
        agendamentoModal.show();
    }

    agendamentoModalElement.addEventListener('hidden.bs.modal', () => {
        calendar.unselect();
    });

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
        
        if (!data.id_recurso) {
            Swal.fire('Erro!', 'Ocorreu um problema ao selecionar o recurso. Tente novamente.', 'error');
            return;
        }

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
            
            if (selectedDate && availabilityContainer.style.display === 'block') {
                 const currentApi = calendar.getApi ? calendar.getApi() : calendar; 
                 currentApi.trigger('dateClick', { date: selectedDate });
            }
            Swal.fire('Sucesso!', 'Agendamento salvo com sucesso.', 'success');
        })
        .catch(error => {
            const errorMessage = error.errors ? Object.values(error.errors).flat().join('\n') : (error.message || 'Ocorreu um erro.');
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
                        if (selectedDate && availabilityContainer.style.display === 'block') {
                             const currentApi = calendar.getApi ? calendar.getApi() : calendar;
                             currentApi.trigger('dateClick', { date: selectedDate });
                        }
                        Swal.fire('Excluído!', 'O agendamento foi excluído.', 'success');
                    } else { 
                        return response.json().then(err => {
                            throw new Error(err.message || 'Falha ao excluir.');
                        });
                     }
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