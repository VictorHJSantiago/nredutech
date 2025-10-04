import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('backup_file');
    const fileChosenText = document.getElementById('file-chosen-text');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Nenhum arquivo escolhido';
            fileChosenText.textContent = fileName;
        });
    }

    const restoreForm = document.getElementById('restore-form');
    const confirmButton = document.getElementById('confirm-restore-button');

    if (restoreForm && confirmButton) {
        confirmButton.addEventListener('click', function(event) {
            event.preventDefault();
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.fire({
                    title: 'Nenhum Arquivo Selecionado',
                    text: 'Por favor, escolha um arquivo de backup (.sql) antes de continuar.',
                    icon: 'error',
                    confirmButtonColor: '#0169b4',
                    confirmButtonText: 'Entendi'
                });
                return;
            }

            Swal.fire({
                title: 'Atenção: Ação Irreversível!',
                html: "Você tem certeza que deseja restaurar este backup?<br><br><strong>TODOS OS DADOS ATUAIS SERÃO APAGADOS E SUBSTITUÍDOS PERMANENTEMENTE.</strong>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, restaurar agora!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    restoreForm.submit();
                }
            });
        });
    }
});