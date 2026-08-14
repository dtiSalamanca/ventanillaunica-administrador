$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // ---- Elementos DOM ----
    const tablaActivos = $("#tabla-ciudadanos-activos");
    const tablaSinVerificar = $("#tabla-ciudadanos-sin-verificar");
    const tablaBloqueados = $("#tabla-ciudadanos-bloqueados");
    const botonRecargar = $("#btn-recargar-ciudadanos");
    const selectionCountActivos = $("#selection-count-activos");
    const selectionCountSinVerificar = $("#selection-count-sin-verificar");
    const selectionCountBloqueados = $("#selection-count-bloqueados");
    const btnBloquear = $("#btn-bloquear-ciudadanos");
    const btnBloquearSinVerificar = $("#btn-bloquear-sin-verificar");
    const btnDesbloquear = $("#btn-desbloquear-ciudadanos");

    if (
        !tablaActivos.length ||
        !tablaSinVerificar.length ||
        !tablaBloqueados.length
    ) {
        return;
    }

    // ---- Estado de selección ----
    const ciudadanosSeleccionados = new Set();

    // ---- Utilerías ----
    function escapeHtml(value) {
        return $("<div>").text(value).html();
    }

    // ---- Idioma DataTables ----
    const language = {
        processing: "Procesando...",
        lengthMenu: "Mostrar _MENU_ registros",
        zeroRecords: "No se encontraron resultados",
        emptyTable: "Ningún ciudadano disponible en esta tabla",
        info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        infoFiltered: "(filtrado de un total de _MAX_ registros)",
        search: "Buscar:",
        infoThousands: ",",
        loadingRecords: "Cargando...",
        paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior",
        },
    };

    // ---- Columnas con checkbox ----
    const checkboxColumn = {
        data: null,
        className: "w-checkbox dt-center",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
            if (type === "display") {
                const checked = ciudadanosSeleccionados.has(String(row.id))
                    ? "checked"
                    : "";
                return (
                    '<input type="checkbox" class="ciudadano-checkbox" value="' +
                    escapeHtml(row.id) +
                    '" ' +
                    checked +
                    ">"
                );
            }
            return "";
        },
    };

    const dataColumns = [
        {
            data: "nombre_completo",
            className: "w-nombre",
            render: $.fn.dataTable.render.text(),
        },
        {
            data: "email",
            className: "w-correo",
            render: $.fn.dataTable.render.text(),
        },
        {
            data: "fecha_registro",
            className: "w-registro",
            render: $.fn.dataTable.render.text(),
        },
        {
            data: "activo",
            className: "w-estado",
            render: function (data, type, row) {
                if (type !== "display") {
                    return data;
                }
                if (!row.activo) {
                    return (
                        '<span class="ciudadano-badge ciudadano-badge-bloqueado">' +
                        '<i class="fas fa-ban"></i> Bloqueado</span>'
                    );
                }
                if (!row.verificado) {
                    return (
                        '<span class="ciudadano-badge ciudadano-badge-sin-verificar">' +
                        '<i class="fas fa-envelope"></i> Sin verificar</span>'
                    );
                }
                return (
                    '<span class="ciudadano-badge ciudadano-badge-activo">' +
                    '<i class="fas fa-check-circle"></i> Activo</span>'
                );
            },
        },
    ];

    const columnsConCheckbox = [checkboxColumn].concat(dataColumns);

    const dataTableOptions = {
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        language: language,
        data: [],
    };

    const dataTableActivos = tablaActivos.DataTable(
        $.extend({}, dataTableOptions, {
            columns: columnsConCheckbox,
        }),
    );

    const dataTableSinVerificar = tablaSinVerificar.DataTable(
        $.extend({}, dataTableOptions, {
            columns: columnsConCheckbox,
            language: $.extend({}, language, {
                emptyTable: "Ningún ciudadano sin verificar disponible",
            }),
        }),
    );

    const dataTableBloqueados = tablaBloqueados.DataTable(
        $.extend({}, dataTableOptions, {
            columns: columnsConCheckbox,
            language: $.extend({}, language, {
                emptyTable: "Ningún ciudadano bloqueado disponible",
            }),
        }),
    );

    // ---- Manejo de selección ----
    function actualizarSeleccion() {
        const total = ciudadanosSeleccionados.size;
        selectionCountActivos.text(total + " ciudadano(s) seleccionado(s)");
        selectionCountSinVerificar.text(
            total + " ciudadano(s) seleccionado(s)",
        );
        selectionCountBloqueados.text(total + " ciudadano(s) seleccionado(s)");
        btnBloquear.prop("disabled", total === 0);
        btnBloquearSinVerificar.prop("disabled", total === 0);
        btnDesbloquear.prop("disabled", total === 0);
    }

    function sincronizarCheckboxes(tablaId) {
        const table = $("#" + tablaId).DataTable();
        table.rows().every(function () {
            const row = this.data();
            if (!row) return;
            const rowNode = this.node();
            if (!rowNode) return;
            const checkbox = $(rowNode).find(".ciudadano-checkbox");
            if (checkbox.length) {
                checkbox.prop(
                    "checked",
                    ciudadanosSeleccionados.has(String(row.id)),
                );
            }
        });
    }

    $(document).on("change", ".ciudadano-checkbox", function () {
        const id = $(this).val();
        if ($(this).is(":checked")) {
            ciudadanosSeleccionados.add(id);
        } else {
            ciudadanosSeleccionados.delete(id);
        }
        actualizarSeleccion();
    });

    // Al redibujar DataTable (paginación, búsqueda, etc.), restaurar checkboxes
    tablaActivos.on("draw.dt", function () {
        sincronizarCheckboxes("tabla-ciudadanos-activos");
    });
    tablaSinVerificar.on("draw.dt", function () {
        sincronizarCheckboxes("tabla-ciudadanos-sin-verificar");
    });
    tablaBloqueados.on("draw.dt", function () {
        sincronizarCheckboxes("tabla-ciudadanos-bloqueados");
    });

    // ---- Carga de datos ----
    function setLoading(isLoading) {
        botonRecargar.prop("disabled", isLoading);
        botonRecargar.toggleClass("is-loading", isLoading);
        botonRecargar.find("i").toggleClass("fa-spin", isLoading);
    }

    function actualizarTablas(ciudadanos) {
        const activos = ciudadanos.filter(function (ciudadano) {
            return ciudadano.activo === true && ciudadano.verificado === true;
        });
        const sinVerificar = ciudadanos.filter(function (ciudadano) {
            return ciudadano.activo === true && ciudadano.verificado !== true;
        });
        const bloqueados = ciudadanos.filter(function (ciudadano) {
            return ciudadano.activo !== true;
        });

        ciudadanosSeleccionados.clear();
        actualizarSeleccion();

        dataTableActivos.clear().rows.add(activos).draw();
        dataTableSinVerificar.clear().rows.add(sinVerificar).draw();
        dataTableBloqueados.clear().rows.add(bloqueados).draw();
    }

    function cargarCiudadanos() {
        setLoading(true);

        $.ajax({
            url: window.ciudadanosRoutes.index,
            type: "GET",
            dataType: "json",
        })
            .done(function (ciudadanos) {
                actualizarTablas(Array.isArray(ciudadanos) ? ciudadanos : []);
            })
            .fail(function (xhr) {
                var message =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Ocurrió un error al consultar los ciudadanos.";

                actualizarTablas([]);

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: message,
                });
            })
            .always(function () {
                setLoading(false);
            });
    }

    botonRecargar.on("click", function () {
        cargarCiudadanos();
    });

    // ---- Bloquear ciudadanos ----
    function confirmarBloqueo(btn) {
        const total = ciudadanosSeleccionados.size;
        if (total === 0) return;

        Swal.fire({
            icon: "warning",
            title: "¿Bloquear ciudadanos?",
            text:
                "Se bloquearán " +
                total +
                " ciudadano(s). Ya no podrán acceder a la Ventanilla Única.",
            showCancelButton: true,
            confirmButtonColor: "#c62828",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, bloquear",
            cancelButtonText: "Cancelar",
        }).then(function (result) {
            if (!result.isConfirmed) return;

            btn.prop("disabled", true);

            $.ajax({
                url: window.ciudadanosRoutes.bloquear,
                type: "POST",
                dataType: "json",
                data: {
                    ciudadanos: Array.from(ciudadanosSeleccionados).map(Number),
                },
            })
                .done(function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Bloqueados!",
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    cargarCiudadanos();
                })
                .fail(function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: obtenerMensajeError(
                            xhr,
                            "Ocurrió un error al bloquear los ciudadanos.",
                        ),
                    });
                })
                .always(function () {
                    btn.prop("disabled", ciudadanosSeleccionados.size === 0);
                });
        });
    }

    btnBloquear.on("click", function () {
        confirmarBloqueo(btnBloquear);
    });
    btnBloquearSinVerificar.on("click", function () {
        confirmarBloqueo(btnBloquearSinVerificar);
    });

    // ---- Desbloquear ciudadanos ----
    btnDesbloquear.on("click", function () {
        const total = ciudadanosSeleccionados.size;
        if (total === 0) return;

        Swal.fire({
            icon: "question",
            title: "¿Desbloquear ciudadanos?",
            text:
                "Se desbloquearán " +
                total +
                " ciudadano(s) y podrán volver a acceder a la Ventanilla Única.",
            showCancelButton: true,
            confirmButtonColor: "#601028",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, desbloquear",
            cancelButtonText: "Cancelar",
        }).then(function (result) {
            if (!result.isConfirmed) return;

            btnDesbloquear.prop("disabled", true);

            $.ajax({
                url: window.ciudadanosRoutes.desbloquear,
                type: "POST",
                dataType: "json",
                data: {
                    ciudadanos: Array.from(ciudadanosSeleccionados).map(Number),
                },
            })
                .done(function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Desbloqueados!",
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    cargarCiudadanos();
                })
                .fail(function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: obtenerMensajeError(
                            xhr,
                            "Ocurrió un error al desbloquear los ciudadanos.",
                        ),
                    });
                })
                .always(function () {
                    btnDesbloquear.prop(
                        "disabled",
                        ciudadanosSeleccionados.size === 0,
                    );
                });
        });
    });

    // ---- Utilería de mensajes de error ----
    function obtenerMensajeError(xhr, fallback) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            const parts = [];
            const errors = xhr.responseJSON.errors;
            for (const key in errors) {
                if (errors.hasOwnProperty(key)) {
                    parts.push(errors[key].join(", "));
                }
            }
            if (parts.length) {
                return parts.join("\n");
            }
        }
        return fallback;
    }

    // ---- Inicializar ----
    cargarCiudadanos();
});
