document.addEventListener('DOMContentLoaded', () => {
    const downloadTrigger = document.getElementById('backupDownloadTrigger');

    if (downloadTrigger && downloadTrigger.dataset.url) {
        window.location.href = downloadTrigger.dataset.url;
    }
});