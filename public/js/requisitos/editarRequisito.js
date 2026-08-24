document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("form-editar-requisito");
    const botonActualizar = document.getElementById("btn-actualizar-requisito");

    if (!formulario || !botonActualizar) return;

    const campos = [
        { inputId: "nombre", counterId: "counter-nombre", max: 255 },
    ];

    campos.forEach(({ inputId, counterId, max }) => {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);
        if (!input || !counter) return;

        const actualizar = () => {
            const len = input.value.length;
            counter.textContent = `${len} / ${max}`;
            counter.classList.remove("warn", "danger");
            if (len >= Math.floor(max * 0.95)) {
                counter.classList.add("danger");
            } else if (len >= Math.floor(max * 0.8)) {
                counter.classList.add("warn");
            }
        };

        input.addEventListener("input", actualizar);
        input.addEventListener("blur", () => {
            input.value = input.value.trim();
            actualizar();
        });

        actualizar();
    });

    formulario.addEventListener("submit", function () {
        botonActualizar.disabled = true;
        botonActualizar.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i>Actualizando...';
    });
});
