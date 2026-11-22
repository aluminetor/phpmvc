const indicador = document.querySelector('[data-cronometro][data-en-marcha="1"]');

if (indicador) {
    const base = Number(indicador.dataset.cronometro);
    const inicio = performance.now();
    let ultimoTiempo = indicador.textContent;

    const rellenar = (valor, digitos = 2) => String(valor).padStart(digitos, '0');

    function actualizar(ahora) {
        const milisegundos = Math.floor(base + ahora - inicio);
        const segundos = Math.floor(milisegundos / 1000);
        const tiempo = [
            rellenar(Math.floor(segundos / 3600)),
            rellenar(Math.floor(segundos / 60) % 60),
            rellenar(segundos % 60),
        ].join(':') + '.' + rellenar(milisegundos % 1000, 3);

        if (tiempo !== ultimoTiempo) {
            indicador.textContent = tiempo;
            ultimoTiempo = tiempo;
        }

        requestAnimationFrame(actualizar);
    }

    requestAnimationFrame(actualizar);
}
