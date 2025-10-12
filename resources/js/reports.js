
function toggleStatusFilterVisibility() {
    const reportTypeSelect = document.getElementById('report_type');
    const filterDiv = document.getElementById('status-agendamento-filter');
    
    if (reportTypeSelect && filterDiv) {
        if (reportTypeSelect.value === 'agendamentos_status') {
            filterDiv.style.display = 'block';
        } else {
            filterDiv.style.display = 'none';
        }
    }
}

function toggleDropdown() {
    document.getElementById("downloadDropdownMenu").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.matches('.btn-download-toggle, .btn-download-toggle *')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}


function renderRecursosChart(chartData) {
    const recursosData = chartData.recursosPorStatus || {};
    const recursosCtx = document.getElementById('recursosStatusChart');
    
    if (recursosCtx && Object.keys(recursosData).length > 0) {
        new Chart(recursosCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(recursosData),
                datasets: [{
                    label: 'Recursos por Status',
                    data: Object.values(recursosData),
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Recursos por Status' }
                }
            }
        });
    }
}

function renderUsuariosMunicipioChart(chartData) {
    const usuariosData = chartData.usuariosPorMunicipio || {};
    const usuariosCtx = document.getElementById('usuariosMunicipioChart');

    if (usuariosCtx && Object.keys(usuariosData).length > 0) {
        new Chart(usuariosCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(usuariosData),
                datasets: [{
                    label: 'Total de Usuários',
                    data: Object.values(usuariosData),
                    backgroundColor: '#007bff',
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                indexAxis: 'y',
                plugins: {
                    title: { display: true, text: 'Usuários por Município' }
                }
            }
        });
    }
}

function renderUsuariosTipoChart(chartData) {
    const usuariosTipoData = chartData.usuariosTipo || {};
    const usuariosTipoCtx = document.getElementById('usuariosTipoChart');

    if (usuariosTipoCtx && Object.keys(usuariosTipoData).length > 0) {
        new Chart(usuariosTipoCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(usuariosTipoData).map(s => s.charAt(0).toUpperCase() + s.slice(1)), 
                datasets: [{
                    label: 'Total de Usuários',
                    data: Object.values(usuariosTipoData),
                    backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Usuários por Tipo' }
                }
            }
        });
    }
}



document.addEventListener('DOMContentLoaded', function () {
    const reportTypeSelect = document.getElementById('report_type');
    if (reportTypeSelect) {
        reportTypeSelect.addEventListener('change', toggleStatusFilterVisibility);
        toggleStatusFilterVisibility(); 
    }

    const chartDataEl = document.getElementById('chart-data');
    if (chartDataEl) {
        const chartData = {
            recursosPorStatus: JSON.parse(chartDataEl.dataset.recursosStatus || '{}'),
            usuariosPorMunicipio: JSON.parse(chartDataEl.dataset.usuariosMunicipio || '{}'),
            usuariosTipo: JSON.parse(chartDataEl.dataset.usuariosTipo || '{}')
        };
        
        renderRecursosChart(chartData);
        renderUsuariosMunicipioChart(chartData);
        renderUsuariosTipoChart(chartData);
    }
});