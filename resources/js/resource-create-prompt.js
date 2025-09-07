import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    
    const resourceForm = document.querySelector('form.material-form');
    if (!resourceForm) {
        return; 
    }

    let submissionConfirmed = false;
    const MAX_SPLIT_LIMIT = 50; 

    resourceForm.addEventListener('submit', function (event) {
        
        if (submissionConfirmed) {
            return true; 
        }

        const quantityInput = document.getElementById('quantidade');
        const quantity = parseInt(quantityInput.value, 10);

        if (quantity <= 1 || isNaN(quantity)) {
            submissionConfirmed = true; 
            return true;
        }

        if (quantity > MAX_SPLIT_LIMIT) {
            event.preventDefault(); 

            Swal.fire({
                title: 'Limite de Divisão Excedido',
                text: `Não é possível criar em massa (individualmente) mais de ${MAX_SPLIT_LIMIT} itens. Você digitou ${quantity}. Deseja continuar e salvar este item como um LOTE ÚNICO com ${quantity} unidades?`,
                icon: 'warning',
                
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                
                showConfirmButton: true,
                confirmButtonText: `Sim, Continuar (Salvar como Lote)`,
                
            }).then((result) => {
                if (result.isConfirmed) {
                    submissionConfirmed = true;
                    resourceForm.submit(); 
                }
            });
            
            return; 
        }

        event.preventDefault();

        Swal.fire({
            title: 'Confirmar Cadastro de Quantidade',
            text: `Você inseriu a quantidade ${quantity}. Como deseja cadastrar este(s) item(ns)?`,
            icon: 'question',
            
            showConfirmButton: true,
            confirmButtonText: `Cadastrar ${quantity} Itens Individuais (Qtde. 1 cada)`,

            showDenyButton: true,
            denyButtonText: `Cadastrar 1 Lote Único (Qtde. ${quantity})`,

            showCancelButton: true,
            cancelButtonText: 'Cancelar'

        }).then((result) => {

            if (result.isConfirmed) {
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'split_quantity');
                hiddenInput.setAttribute('value', 'true');
                resourceForm.appendChild(hiddenInput);

                submissionConfirmed = true; 
                resourceForm.submit(); 
            
            } else if (result.isDenied) {
                submissionConfirmed = true; 
                resourceForm.submit(); 
            
            }
        });
    });
});