document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    buscarPorFecha();
}

function buscarPorFecha() {
    const inputFecha = document.querySelector('#fecha');

    inputFecha.addEventListener('input', e => {
        const fecha = e.target.value;
        window.location = `?fecha=${fecha}`;
    });
}