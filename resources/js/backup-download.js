
import Swal from 'sweetalert2';

function handleAutoDownload() {
    const downloadTrigger = document.getElementById('backupDownloadTrigger');
    if (downloadTrigger && downloadTrigger.dataset.url) {
        setTimeout(() => {
            window.location.href = downloadTrigger.dataset.url;
        }, 100);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    handleAutoDownload();
});
