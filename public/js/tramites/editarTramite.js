document.addEventListener("DOMContentLoaded", function () {
    var formulario = document.getElementById("form-editar-tramite");
    var botonActualizar = document.getElementById("btn-actualizar-tramite");
    var selectDependencia = document.getElementById("fk_dependencia");

    if (!formulario || !botonActualizar) return;

    // Cargar dependencias activas en el select y activar Select2
    if (selectDependencia && window.dependenciasRoutes && window.dependenciasRoutes.activas) {
        fetch(window.dependenciasRoutes.activas)
            .then(function (response) {
                if (!response.ok) throw new Error("Error al cargar dependencias");
                return response.json();
            })
            .then(function (dependencias) {
                selectDependencia.innerHTML =
                    '<option value="" disabled selected>Seleccione una dependencia</option>';

                if (dependencias.length === 0) {
                    selectDependencia.innerHTML +=
                        '<option value="" disabled>No hay dependencias disponibles</option>';
                } else {
                    dependencias.forEach(function (dep) {
                        var option = document.createElement("option");
                        option.value = dep.id_dependencia;
                        option.textContent = dep.nombre;
                        selectDependencia.appendChild(option);
                    });
                }

                var selectedValue = selectDependencia.getAttribute("data-selected");
                if (selectedValue) {
                    selectDependencia.value = selectedValue;
                }

                // Inicializar Select2
                jQuery(selectDependencia).select2({
                    language: "es",
                    placeholder: "Seleccione una dependencia",
                    allowClear: true,
                    width: "100%",
                });

                // Reflejar clase is-invalid en Select2 si el select tiene error
                if (selectDependencia.classList.contains("is-invalid")) {
                    jQuery(selectDependencia).on("select2:open", function () {
                        var container = jQuery(selectDependencia)
                            .data("select2")
                            .$container.addClass("select2-container--error");
                    });
                }
            })
            .catch(function () {
                selectDependencia.innerHTML =
                    '<option value="" disabled>Error al cargar dependencias</option>';
            });
    }

    // Contador de caracteres para el nombre
    var campos = [
        { inputId: "nombre", counterId: "counter-nombre", max: 255 },
    ];

    campos.forEach(function (campo) {
        var input = document.getElementById(campo.inputId);
        var counter = document.getElementById(campo.counterId);
        if (!input || !counter) return;

        var actualizar = function () {
            var len = input.value.length;
            counter.textContent = len + " / " + campo.max;
            counter.classList.remove("warn", "danger");
            if (len >= Math.floor(campo.max * 0.95)) {
                counter.classList.add("danger");
            } else if (len >= Math.floor(campo.max * 0.8)) {
                counter.classList.add("warn");
            }
        };

        input.addEventListener("input", actualizar);
        input.addEventListener("blur", function () {
            input.value = input.value.trim();
            actualizar();
        });

        actualizar();
    });

    // Deshabilitar botón al enviar
    formulario.addEventListener("submit", function () {
        botonActualizar.disabled = true;
        botonActualizar.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i>Actualizando...';
    });
});
