document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent'); 
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation(); 
            sidebar.classList.toggle('is-open');
        });
    }

    if (mainContent) {
        mainContent.addEventListener('click', () => {
            if (sidebar.classList.contains('is-open')) {
                sidebar.classList.remove('is-open');
            }
        });
    }

    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => {
            if (sidebar.classList.contains('is-open')) {
                setTimeout(() => {
                    sidebar.classList.remove('is-open');
                }, 100); 
            }
        });
    });
});