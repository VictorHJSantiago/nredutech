document.addEventListener('DOMContentLoaded', () => {
    const backdrop = document.getElementById('deleteModalBackdrop');
    const btnCancel = document.getElementById('modalButtonCancel');
    const btnConfirm = document.getElementById('modalButtonConfirm');
    
    const deleteForms = document.querySelectorAll('.form-delete');

    let formToSubmit = null; 

    if (!backdrop || !btnCancel || !btnConfirm) {
        return;
    }

    const showModal = (form) => {
        formToSubmit = form; 
        backdrop.classList.add('is-open');
    };

    const hideModal = () => {
        backdrop.classList.remove('is-open');
        formToSubmit = null; 
    };

    deleteForms.forEach(form => {
        form.addEventListener('submit', (event) => {
            event.preventDefault(); 
            showModal(form);        
        });
    });

    btnCancel.addEventListener('click', () => {
        hideModal();
    });

    btnConfirm.addEventListener('click', () => {
        if (formToSubmit) {
            formToSubmit.submit(); 
        }
        hideModal();
    });

    backdrop.addEventListener('click', (event) => {
        if (event.target === backdrop) {
            hideModal();
        }
    });
});