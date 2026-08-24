document.addEventListener("DOMContentLoaded", function () {
    var formulario = document.getElementById("form-editar-tramite");
    var botonActualizar = document.getElementById("btn-actualizar-tramite");
    var selectDependencia = document.getElementById("fk_dependencia");
    var selectCuentaCri = document.getElementById("fk_cri");

    if (!formulario || !botonActualizar) return;

    function applySelect2ErrorClass(selectElement) {
        if (!selectElement || !selectElement.classList.contains("is-invalid")) {
            return;
        }

        jQuery(selectElement).on("select2:open", function () {
            var container = jQuery(selectElement).data("select2").$container;
            container.addClass("select2-container--error");
        });
    }

    function normalizeSelectOptions(payload) {
        if (Array.isArray(payload)) {
            return payload;
        }

        if (payload && Array.isArray(payload.data)) {
            return payload.data;
        }

        if (payload && Array.isArray(payload.cuentas)) {
            return payload.cuentas;
        }

        if (payload && Array.isArray(payload.result)) {
            return payload.result;
        }

        if (payload && Array.isArray(payload.items)) {
            return payload.items;
        }

        if (payload && typeof payload === "object") {
            var arrayField = Object.keys(payload).find(function (key) {
                return Array.isArray(payload[key]);
            });

            return arrayField ? payload[arrayField] : [];
        }

        return [];
    }

    function resolveOptionValueAndText(item) {
        if (!item || typeof item !== "object") {
            return {
                value: String(item ?? ""),
                text: String(item ?? ""),
            };
        }

        var valueKeys = [
            "id",
            "id_cuenta",
            "idCuenta",
            "cuenta_id",
            "value",
            "codigo",
            "codigo_cuenta",
            "account_code",
            "clave",
            "id_cri",
        ];

        var optionValue = "";
        var optionText = "";

        valueKeys.forEach(function (key) {
            if (
                optionValue !== "" ||
                !Object.prototype.hasOwnProperty.call(item, key)
            ) {
                return;
            }

            var candidate = item[key];
            if (candidate !== null && candidate !== "") {
                optionValue = String(candidate);
            }
        });

        if (!optionValue) {
            optionValue = String(item.id ?? "");
        }

        var accountCode =
            item.account_code ??
            item.codigo ??
            item.codigo_cuenta ??
            item.code ??
            "";
        var accountName =
            item.account_name ??
            item.nombre ??
            item.nombre_cuenta ??
            item.cuenta ??
            item.descripcion ??
            item.label ??
            "";

        if (accountCode && accountName) {
            optionText = accountCode + " - " + accountName;
        } else if (accountCode) {
            optionText = accountCode;
        } else if (accountName) {
            optionText = accountName;
        } else {
            optionText = optionValue || "Sin nombre";
        }

        return {
            value: optionValue,
            text: optionText,
        };
    }

    // Cargar dependencias activas en el select y activar Select2
    if (
        selectDependencia &&
        window.dependenciasRoutes &&
        window.dependenciasRoutes.activas
    ) {
        fetch(window.dependenciasRoutes.activas)
            .then(function (response) {
                if (!response.ok)
                    throw new Error("Error al cargar dependencias");
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
                        option.textContent = dep.nombre_dependencia;
                        selectDependencia.appendChild(option);
                    });
                }

                var selectedValue =
                    selectDependencia.getAttribute("data-selected");
                if (selectedValue) {
                    selectDependencia.value = selectedValue;
                }

                jQuery(selectDependencia).select2({
                    language: "es",
                    placeholder: "Seleccione una dependencia",
                    allowClear: true,
                    width: "100%",
                });

                applySelect2ErrorClass(selectDependencia);
            })
            .catch(function () {
                selectDependencia.innerHTML =
                    '<option value="" disabled>Error al cargar dependencias</option>';
            });
    }

    if (
        selectCuentaCri &&
        window.dependenciasRoutes &&
        window.dependenciasRoutes.cuentasCri
    ) {
        fetch(window.dependenciasRoutes.cuentasCri)
            .then(function (response) {
                if (!response.ok)
                    throw new Error("Error al cargar cuentas CRI");
                return response.json();
            })
            .then(function (payload) {
                var cuentas = normalizeSelectOptions(payload);
                selectCuentaCri.innerHTML =
                    '<option value="" disabled selected>Seleccione una cuenta contable</option>';

                if (cuentas.length === 0) {
                    selectCuentaCri.innerHTML +=
                        '<option value="" disabled>No hay cuentas disponibles</option>';
                } else {
                    cuentas.forEach(function (cuenta) {
                        var option = document.createElement("option");
                        var optionData = resolveOptionValueAndText(cuenta);
                        option.value = optionData.value;
                        option.textContent = optionData.text;
                        selectCuentaCri.appendChild(option);
                    });
                }

                var selectedValue =
                    selectCuentaCri.getAttribute("data-selected");
                if (selectedValue) {
                    selectCuentaCri.value = selectedValue;
                }

                jQuery(selectCuentaCri).select2({
                    language: "es",
                    placeholder: "Seleccione una cuenta contable",
                    allowClear: true,
                    width: "100%",
                });

                applySelect2ErrorClass(selectCuentaCri);
            })
            .catch(function () {
                selectCuentaCri.innerHTML =
                    '<option value="" disabled>Error al cargar cuentas CRI</option>';
            });
    }

    // Contador de caracteres para el nombre
    var campos = [{ inputId: "nombre", counterId: "counter-nombre", max: 255 }];

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

    // Toggle: nota de cobro por metro cuadrado
    var checkCobraM2 = document.getElementById("cobra_por_m2");
    var notaPrecioPorM2 = document.getElementById("nota-precio-por-m2");

    function toggleNotaPorM2() {
        var esPorM2 = checkCobraM2 && checkCobraM2.checked;

        if (notaPrecioPorM2) {
            notaPrecioPorM2.classList.toggle("d-none", !esPorM2);
        }
    }

    if (checkCobraM2) {
        checkCobraM2.addEventListener("change", toggleNotaPorM2);
        toggleNotaPorM2();
    }

    // Deshabilitar botón al enviar
    formulario.addEventListener("submit", function () {
        botonActualizar.disabled = true;
        botonActualizar.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i>Actualizando...';
    });
});
