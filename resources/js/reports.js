function toggleDropdown() {
    const menu = document.getElementById("downloadDropdownMenu");
    if (menu) {
        menu.classList.toggle("show");
    } else {
        console.error("Dropdown menu element not found!");
    }
}

let recursosStatusChartInstance = null;
let usuariosMunicipioChartInstance = null;
let usuariosTipoChartInstance = null;
let turmasTurnoChartInstance = null;
let componentesStatusChartInstance = null;

function renderChart(canvasId, chartType, chartData, chartOptions, instanceVariableSetter) {
    const ctx = document.getElementById(canvasId);
    const container = ctx ? ctx.closest('.chart-container') : null;

    if (!ctx || !container) {
        return;
    }

    if (instanceVariableSetter.instance) {
        try {
            instanceVariableSetter.instance.destroy();
        } catch (e) {
            console.error(`Erro ao destruir gráfico ${canvasId}:`, e);
        }
        instanceVariableSetter.instance = null;
    }

    const labels = chartData.labels || [];
    const dataValues = chartData.datasets?.[0]?.data || [];
    const hasValidData = labels.length > 0 && dataValues.length > 0 && dataValues.some(v => v > 0);

    if (hasValidData) {
        container.style.display = 'flex';
        try {
            instanceVariableSetter.instance = new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: chartOptions
            });
        } catch (e) {
            console.error(`Erro ao criar gráfico ${canvasId}:`, e);
            container.style.display = 'none';
        }
    } else {
        container.style.display = 'none';
    }
}

function renderRecursosChart(chartData) {
    const data = chartData.recursosPorStatus || {};
    const labels = Object.keys(data).map(status => {
        switch(status) {
            case 'funcionando': return 'Funcionando';
            case 'em_manutencao': return 'Em Manutenção';
            case 'quebrado': return 'Quebrado';
            case 'descartado': return 'Descartado';
            default: return status.charAt(0).toUpperCase() + status.slice(1);
        }
    });
    const values = Object.values(data);

    renderChart('recursosStatusChart', 'doughnut', {
        labels: labels,
        datasets: [{
            data: values,
            backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8', '#fd7e14'],
        }]
    }, {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } },
            title: { display: false },
            tooltip: { callbacks: { label: context => ` ${context.label || ''}: ${context.parsed || 0}` } }
        }
    }, { set instance(val) { recursosStatusChartInstance = val; }, get instance() { return recursosStatusChartInstance; } });
}

function renderUsuariosMunicipioChart(chartData) {
    const data = chartData.usuariosPorMunicipio || {};
    const labels = Object.keys(data);
    const values = Object.values(data);

    renderChart('usuariosMunicipioChart', 'bar', {
        labels: labels,
        datasets: [{
            label: 'Total de Usuários',
            data: values,
            backgroundColor: 'rgba(1, 105, 180, 0.7)',
            borderColor: 'rgba(1, 105, 180, 1)',
            borderWidth: 1
        }]
    }, {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        scales: { x: { beginAtZero: true, grid: { display: false } }, y: { grid: { display: false } } },
        plugins: {
            legend: { display: false }, title: { display: false },
            tooltip: { callbacks: { label: context => ` ${context.dataset.label || ''}: ${context.parsed.x || 0}` } }
        }
    }, { set instance(val) { usuariosMunicipioChartInstance = val; }, get instance() { return usuariosMunicipioChartInstance; } });
}

function renderUsuariosTipoChart(chartData) {
    const data = chartData.usuariosTipo || {};
     const labels = Object.keys(data).map(tipo => tipo.charAt(0).toUpperCase() + tipo.slice(1));
    const values = Object.values(data);

    renderChart('usuariosTipoChart', 'pie', {
        labels: labels,
        datasets: [{
            data: values,
            backgroundColor: ['#0169b4', '#5fb13b', '#ffc107', '#dc3545'],
        }]
    }, {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }, title: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) { label += ': '; }
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const value = context.parsed || 0;
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                        label += `${value} (${percentage})`;
                        return label;
                    }
                }
            }
        }
    }, { set instance(val) { usuariosTipoChartInstance = val; }, get instance() { return usuariosTipoChartInstance; } });
}

function renderTurmasTurnoChart(chartData) {
    const data = chartData.turmasPorTurno || {};
    const labels = Object.keys(data).map(t => t.charAt(0).toUpperCase() + t.slice(1));
    const values = Object.values(data);

    renderChart('turmasTurnoChart', 'pie', {
        labels: labels,
        datasets: [{ data: values, backgroundColor: ['#ffc107', '#17a2b8', '#6f42c1', '#6c757d'] }]
    }, {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }, title: { display: false },
            tooltip: { 
                callbacks: {
                    label: function(context) {
                         let label = context.label || '';
                         if (label) { label += ': '; }
                         const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                         const value = context.parsed || 0;
                         const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                         label += `${value} (${percentage})`;
                         return label;
                    }
                }
            }
        }
    }, { set instance(val) { turmasTurnoChartInstance = val; }, get instance() { return turmasTurnoChartInstance; } });
}

function renderComponentesStatusChart(chartData) {
    const data = chartData.componentesPorStatus || {};
    const labels = Object.keys(data).map(s => {
        switch(s) {
            case 'aprovado': return 'Aprovado';
            case 'pendente': return 'Pendente';
            case 'reprovado': return 'Reprovado';
            default: return s.charAt(0).toUpperCase() + s.slice(1);
        }
    });
    const values = Object.values(data);

    renderChart('componentesStatusChart', 'doughnut', {
        labels: labels,
        datasets: [{ data: values, backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'] }]
    }, {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }, title: { display: false },
            tooltip: { callbacks: { label: context => ` ${context.label || ''}: ${context.parsed || 0}` } }
        }
    }, { set instance(val) { componentesStatusChartInstance = val; }, get instance() { return componentesStatusChartInstance; } });
}


function closeAllMultiSelects(exceptThisOne = null) {
    document.querySelectorAll('.custom-multiselect.active').forEach(container => {
        if (container !== exceptThisOne) {
            container.classList.remove('active');
        }
    });
}

function initializeMultiSelects() {
    const containers = document.querySelectorAll('.custom-multiselect');

    containers.forEach(container => {
        const button = container.querySelector('.multiselect-toggle');
        const dropdown = container.querySelector('.multiselect-dropdown');
        const span = button.querySelector('span');
        const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
        const defaultText = span.textContent || 'Selecione...';

        function updateButtonText() {
            const checked = dropdown.querySelectorAll('input[type="checkbox"]:checked');
            if (checked.length === 0) {
                span.textContent = defaultText;
                span.classList.add('default-text'); 
            } else if (checked.length === 1) {
                const label = checked[0].nextElementSibling;
                span.textContent = label ? label.textContent : '1 selecionado';
                span.classList.remove('default-text'); 
            } else {
                span.textContent = `${checked.length} selecionados`;
                span.classList.remove('default-text'); 
            }
        }

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllMultiSelects(container);
            container.classList.toggle('active');
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonText);
        });

        dropdown.addEventListener('click', (e) => {
            if (e.target.tagName !== 'LABEL' && e.target.tagName !== 'INPUT') {
                e.stopPropagation();
            }
        });

        updateButtonText();
    });
}

window.addEventListener('click', function(event) {
    if (!event.target.matches('.btn-download-toggle, .btn-download-toggle *')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            const openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }

    if (!event.target.closest('.custom-multiselect')) {
        closeAllMultiSelects(null);
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const downloadBtn = document.querySelector('.btn-download-toggle');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleDropdown();
        });
    }

    const chartDataEl = document.getElementById('chart-data');
    if (chartDataEl) {
        try {
            const chartData = {
                recursosPorStatus: JSON.parse(chartDataEl.dataset.recursosStatus || '{}'),
                usuariosPorMunicipio: JSON.parse(chartDataEl.dataset.usuariosMunicipio || '{}'),
                usuariosTipo: JSON.parse(chartDataEl.dataset.usuariosTipo || '{}'),
                turmasPorTurno: JSON.parse(chartDataEl.dataset.turmasPorTurno || '{}'),
                componentesPorStatus: JSON.parse(chartDataEl.dataset.componentesStatus || '{}')
            };

            if (document.getElementById('recursosStatusChart')) renderRecursosChart(chartData);
             if (document.getElementById('usuariosMunicipioChart')) renderUsuariosMunicipioChart(chartData);
             if (document.getElementById('usuariosTipoChart')) renderUsuariosTipoChart(chartData);
             if (document.getElementById('turmasTurnoChart')) renderTurmasTurnoChart(chartData);
             if (document.getElementById('componentesStatusChart')) renderComponentesStatusChart(chartData);

        } catch (e) {
            console.error("Erro ao processar ou renderizar gráficos:", e);
             document.querySelectorAll('.chart-container').forEach(container => container.style.display = 'none');
        }
    } else {
        document.querySelectorAll('.chart-container').forEach(container => container.style.display = 'none');
    }

    initializeMultiSelects();
    const downloadDropdown = document.getElementById('downloadDropdownMenu');
    const downloadMessage = document.getElementById('download-message');

    if (downloadDropdown && downloadMessage) {
        const links = downloadDropdown.querySelectorAll('a.download-link');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                downloadMessage.style.display = 'flex'; 
                
                toggleDropdown();

                const url = this.href;

                setTimeout(() => {
                    window.location.href = url;
                }, 100);

                setTimeout(() => {
                     downloadMessage.style.display = 'none';
                }, 10000); 
            });
        });
    }
});