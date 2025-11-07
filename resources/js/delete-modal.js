document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('deleteModal');
    if (!modal) {
        return;
    }

    const modalOverlay = modal.querySelector('.modal-overlay');
    const cancelButton = document.getElementById('cancelButton');
    const itemNameElement = document.getElementById('itemName');
    const deleteForm = document.getElementById('deleteModalForm');
    const deleteButtons = document.querySelectorAll('.delete-button');

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const itemName = button.getAttribute('data-item-name');
            const formAction = button.getAttribute('data-form-action');

            itemNameElement.textContent = itemName;
            deleteForm.setAttribute('action', formAction);
            showModal();
        });
    });

    if (cancelButton) {
        cancelButton.addEventListener('click', hideModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', (event) => {
            if (event.target === modalOverlay) {
                hideModal();
            }
        });
    }

    hideModal();
});