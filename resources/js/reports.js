document.addEventListener('DOMContentLoaded', function () {
    const chartDataEl = document.getElementById('chart-data');
    if (!chartDataEl) {
        return; 
    }

    function renderRecursosChart() {
        const recursosData = JSON.parse(chartDataEl.dataset.recursosStatus || '{}');
        const recursosCtx = document.getElementById('recursosStatusChart');
        
        if (recursosCtx && Object.keys(recursosData).length > 0) {
            new Chart(recursosCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(recursosData),
                    datasets: [{
                        label: 'Total de Recursos',
                        data: Object.values(recursosData),
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false 
                }
            });
        }
    }

    function renderUsuariosChart() {
        const usuariosData = JSON.parse(chartDataEl.dataset.usuariosMunicipio || '{}');
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
                    indexAxis: 'y' 
                }
            });
        }
    }

    function renderUsuariosTipoChart() {
        const usuariosTipoData = JSON.parse(chartDataEl.dataset.usuariosTipo || '{}');
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
                    maintainAspectRatio: false
                }
            });
        }
    }

    renderRecursosChart();
    renderUsuariosChart();
    renderUsuariosTipoChart();
});