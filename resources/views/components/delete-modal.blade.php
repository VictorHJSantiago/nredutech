<div id="deleteModal"
    class="fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-black bg-opacity-50 modal-overlay">
    <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl modal-content">
        <div class="p-6">
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-gray-900">
                    Confirmar Exclusão
                </h3>
            </div>
            <div class="mt-4 modal-body">
                <p class="text-base text-gray-700">
                    Você tem certeza que deseja excluir o item:
                    <strong id="itemName" class="font-bold"></strong>?
                </p>
                <p class="mt-2 text-sm font-semibold text-red-600">
                    Esta ação é irreversível e não pode ser desfeita.
                </p>
            </div>
            <div class="flex items-center justify-end mt-6 space-x-3 modal-footer">
                <button id="cancelButton"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Cancelar
                </button>
                <form id="deleteModalForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>