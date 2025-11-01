import './bootstrap';
import './sidebar';
import './password-toogle';
import './user-create';
import './resource-create-prompt';
import './appointments-calendar';
import './settings-modal';
import './backup-download';
import './restore';
import './reports';

/**
 * Script global para limpar formulários de filtro (GET) antes do envio.
 * Procura por qualquer formulário com a classe 'js-clean-get-form'
 * e desabilita campos de input/select vazios para limpar a URL.
 */
document.addEventListener('DOMContentLoaded', function () {
    const filterForms = document.querySelectorAll('.js-clean-get-form');
    
    filterForms.forEach(form => {
        if (form.method.toLowerCase() === 'get') {
            form.addEventListener('submit', function (event) {
                const inputs = form.querySelectorAll('input[type="text"], input[type="search"], input[type="number"], input[type="date"], select');
                
                inputs.forEach(input => {
                    if (input.value === '') {
                        input.disabled = true;
                    }
                });
            });
        }
    });
});