$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    function autoCerrarAlerta($alerta, ms) {
        setTimeout(function () {
            $alerta.fadeOut(300, function () {
                $(this).remove();
            });
        }, ms || 5000);
    }

    function mostrarAlerta(mensaje, tipo) {
        tipo = tipo || "success";
        var iconos = {
            success: "fa-check-circle",
            warning: "fa-exclamation-triangle",
            error: "fa-times-circle",
            info: "fa-info-circle",
        };
        var icono = iconos[tipo] || "fa-check-circle";
        var $alerta = $(
            '<div class="alert alert-' +
                tipo +
                ' alert-dismissible fade show" role="alert"></div>',
        );
        $alerta.append('<i class="fas ' + icono + ' me-2"></i>');
        $alerta.append($("<span>").text(mensaje));
        $alerta.append(
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        );

        var $contenedor = $("#alertas-dinamicas");
        if (!$contenedor.length) {
            $contenedor = $(".main-container");
        }
        $contenedor.prepend($alerta);
        autoCerrarAlerta($alerta);
    }

    // Cerrar alerta con el botón ×
    $(document).on("click", ".alert-dismissible .btn-close", function () {
        var $alerta = $(this).closest(".alert-dismissible");
        $alerta.fadeOut(300, function () {
            $(this).remove();
        });
    });

    // Las alertas de sesión (crear/actualizar) también se cierran solas a los 5s
    $(".alert-dismissible").each(function () {
        autoCerrarAlerta($(this));
    });

    const tablaActivos = $("#tabla-tramites-activos");
    const tablaInactivos = $("#tabla-tramites-inactivos");

    if (!tablaActivos.length || !tablaInactivos.length) {
        return;
    }

    tablaActivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.tramitesRoutes.activos,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún trámite disponible en esta tabla",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
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
        },
        columns: [
            {
                data: null,
                className: "w-checkbox",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<input type="checkbox" class="tramite-checkbox-activos" value="' +
                        row.id_tramite +
                        '" data-id="' +
                        row.id_tramite +
                        '">'
                    );
                },
            },
            { data: "nombre_tramite", className: "w-tramite" },
            { data: "descripcion_tramite", className: "w-descripcion" },
            {
                data: "cobra_por_m2",
                className: "w-cobro",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.cobra_por_m2) {
                        return '<span class="badge badge-por-m2"><i class="fas fa-ruler-combined me-1"></i>Por m²</span>';
                    }
                    return "";
                },
            },
            {
                data: "vigencia_dias",
                className: "w-vigencia",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.vigencia_dias > 0) {
                        return row.vigencia_dias + " días";
                    }
                    return '<span class="text-muted">—</span>';
                },
            },
        ],
    });

    tablaInactivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.tramitesRoutes.inactivos,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún trámite inactivo disponible",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
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
        },
        columns: [
            {
                data: null,
                className: "w-checkbox",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<input type="checkbox" class="tramite-checkbox-inactivos" value="' +
                        row.id_tramite +
                        '" data-id="' +
                        row.id_tramite +
                        '">'
                    );
                },
            },
            { data: "nombre_tramite", className: "w-tramite" },
            { data: "descripcion_tramite", className: "w-descripcion" },
            {
                data: "cobra_por_m2",
                className: "w-cobro",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.cobra_por_m2) {
                        return '<span class="badge badge-por-m2"><i class="fas fa-ruler-combined me-1"></i>Por m²</span>';
                    }
                    return "";
                },
            },
            {
                data: "vigencia_dias",
                className: "w-vigencia",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.vigencia_dias > 0) {
                        return row.vigencia_dias + " días";
                    }
                    return '<span class="text-muted">—</span>';
                },
            },
        ],
    });

    function getSelectedIds(selector) {
        var ids = [];
        $(selector + ":checked").each(function () {
            ids.push($(this).val());
        });
        return ids;
    }

    // ── Activos actions ──
    function updateActionButtonsActivos() {
        var count = getSelectedIds(".tramite-checkbox-activos").length;
        $("#btn-editar-tramite-activos").prop("disabled", count !== 1);
        $("#btn-revisar-requisitos").prop("disabled", count !== 1);
        $("#btn-revisar-prerequisitos").prop("disabled", count !== 1);
        $("#btn-deshabilitar-tramite").prop("disabled", count === 0);
    }

    $(document).on("change", ".tramite-checkbox-activos", function () {
        if ($(this).prop("checked")) {
            $(".tramite-checkbox-activos").not(this).prop("checked", false);
        }
        updateActionButtonsActivos();
    });

    $(document).on(
        "click",
        "#tabla-tramites-activos tbody tr",
        function (event) {
            var target = $(event.target);
            if (
                target.closest("input, button, a, label, select, textarea")
                    .length
            ) {
                return;
            }

            var selectedRow = $(this);
            if (selectedRow.hasClass("child")) {
                selectedRow = selectedRow.prev();
            }

            var checkbox = selectedRow
                .find(".tramite-checkbox-activos")
                .first();
            if (!checkbox.length) {
                return;
            }

            $(".tramite-checkbox-activos").not(checkbox).prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtonsActivos();
        },
    );

    tablaActivos.on("draw.dt", function () {
        updateActionButtonsActivos();
    });

    $("#btn-editar-tramite-activos").on("click", function () {
        var ids = getSelectedIds(".tramite-checkbox-activos");
        if (ids.length === 1) {
            var url = window.tramitesRoutes.editar.replace("__ID__", ids[0]);
            window.location.href = url;
        }
    });

    $("#btn-revisar-requisitos").on("click", function () {
        var ids = getSelectedIds(".tramite-checkbox-activos");
        if (ids.length === 1) {
            var url = window.tramitesRoutes.requisitos.replace(
                "__ID__",
                ids[0],
            );
            window.location.href = url;
        }
    });

    $("#btn-revisar-prerequisitos").on("click", function () {
        var ids = getSelectedIds(".tramite-checkbox-activos");
        if (ids.length === 1) {
            var url = window.tramitesRoutes.prerequisitos.replace(
                "__ID__",
                ids[0],
            );
            window.location.href = url;
        }
    });

    $("#btn-deshabilitar-tramite").on("click", function () {
        var ids = getSelectedIds(".tramite-checkbox-activos");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "El trámite seleccionado será deshabilitado.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, deshabilitar",
            cancelButtonText: "Cancelar",
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            var url = window.tramitesRoutes.deshabilitar.replace(
                "__ID__",
                ids[0],
            );

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ),
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    mostrarAlerta(data.message, "success");
                    tablaActivos.DataTable().ajax.reload(null, false);
                    tablaInactivos.DataTable().ajax.reload(null, false);
                })
                .catch(function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al deshabilitar el trámite.",
                    });
                });
        });
    });

    updateActionButtonsActivos();

    // ── Inactivos actions ──
    function updateActionButtonsInactivos() {
        var count = getSelectedIds(".tramite-checkbox-inactivos").length;
        $("#btn-habilitar-tramite").prop("disabled", count === 0);
    }

    $(document).on("change", ".tramite-checkbox-inactivos", function () {
        if ($(this).prop("checked")) {
            $(".tramite-checkbox-inactivos").not(this).prop("checked", false);
        }
        updateActionButtonsInactivos();
    });

    $(document).on(
        "click",
        "#tabla-tramites-inactivos tbody tr",
        function (event) {
            var target = $(event.target);
            if (
                target.closest("input, button, a, label, select, textarea")
                    .length
            ) {
                return;
            }

            var selectedRow = $(this);
            if (selectedRow.hasClass("child")) {
                selectedRow = selectedRow.prev();
            }

            var checkbox = selectedRow
                .find(".tramite-checkbox-inactivos")
                .first();
            if (!checkbox.length) {
                return;
            }

            $(".tramite-checkbox-inactivos")
                .not(checkbox)
                .prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtonsInactivos();
        },
    );

    tablaInactivos.on("draw.dt", function () {
        updateActionButtonsInactivos();
    });

    $("#btn-habilitar-tramite").on("click", function () {
        var ids = getSelectedIds(".tramite-checkbox-inactivos");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "El trámite seleccionado será habilitado nuevamente.",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, habilitar",
            cancelButtonText: "Cancelar",
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            var url = window.tramitesRoutes.habilitar.replace("__ID__", ids[0]);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ),
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    mostrarAlerta(data.message, "success");
                    tablaActivos.DataTable().ajax.reload(null, false);
                    tablaInactivos.DataTable().ajax.reload(null, false);
                })
                .catch(function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al habilitar el trámite.",
                    });
                });
        });
    });

    updateActionButtonsInactivos();
});
