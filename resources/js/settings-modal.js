document.addEventListener('DOMContentLoaded', function () {
    
    const backupModalNode = document.getElementById('backupModal');
    if (backupModalNode && backupModalNode.querySelector('.text-danger')) {
        const backupModal = new bootstrap.Modal(backupModalNode);
        backupModal.show();
    }

    const restoreModalNode = document.getElementById('restoreModal');
    if (restoreModalNode && restoreModalNode.querySelector('.text-danger')) {
        const restoreModal = new bootstrap.Modal(restoreModalNode);
        restoreModal.show();
    }
});