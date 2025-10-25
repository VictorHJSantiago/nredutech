
$(document).ready(function() {
    if ($('#id_escola').length) {
        $('#id_escola').select2({
            placeholder: "Digite ou selecione a escola",
            allowClear: true
        });
    }
});