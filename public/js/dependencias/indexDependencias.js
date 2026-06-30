$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const tablaActivos = $("#tabla-dependencias-activas");
    const tablaInactivos = $("#tabla-dependencias-inactivas");

    if (!tablaActivos.length || !tablaInactivos.length) {
        return;
    }

    tablaActivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.dependenciasRoutes.activas,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ninguna dependencia disponible en esta tabla",
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
                        '<input type="checkbox" class="dependencia-checkbox-activas" value="' +
                        row.id_dependencia +
                        '" data-id="' +
                        row.id_dependencia +
                        '">'
                    );
                },
            },
            { data: "nombre_dependencia", className: "w-dependencia" },
        ],
    });

    tablaInactivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.dependenciasRoutes.inactivas,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ninguna dependencia inactiva disponible",
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
                        '<input type="checkbox" class="dependencia-checkbox-inactivas" value="' +
                        row.id_dependencia +
                        '" data-id="' +
                        row.id_dependencia +
                        '">'
                    );
                },
            },
            { data: "nombre_dependencia", className: "w-dependencia" },
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
        var count = getSelectedIds(".dependencia-checkbox-activas").length;
        $("#btn-editar-dependencia-activos").prop("disabled", count !== 1);
        $("#btn-deshabilitar-dependencia").prop("disabled", count === 0);
    }

    $(document).on("change", ".dependencia-checkbox-activas", function () {
        if ($(this).prop("checked")) {
            $(".dependencia-checkbox-activas").not(this).prop("checked", false);
        }
        updateActionButtonsActivos();
    });

    $(document).on(
        "click",
        "#tabla-dependencias-activas tbody tr",
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
                .find(".dependencia-checkbox-activas")
                .first();
            if (!checkbox.length) {
                return;
            }

            $(".dependencia-checkbox-activas").not(checkbox).prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtonsActivos();
        },
    );

    tablaActivos.on("draw.dt", function () {
        updateActionButtonsActivos();
    });

    $("#btn-editar-dependencia-activos").on("click", function () {
        var ids = getSelectedIds(".dependencia-checkbox-activas");
        if (ids.length === 1) {
            var url = window.dependenciasRoutes.editar.replace("__ID__", ids[0]);
            window.location.href = url;
        }
    });

    $("#btn-deshabilitar-dependencia").on("click", function () {
        var ids = getSelectedIds(".dependencia-checkbox-activas");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "La dependencia seleccionada será deshabilitada.",
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

            var url = window.dependenciasRoutes.deshabilitar.replace(
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
                    Swal.fire({
                        icon: "success",
                        title: "Deshabilitado",
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    tablaActivos.DataTable().ajax.reload(null, false);
                    tablaInactivos.DataTable().ajax.reload(null, false);
                })
                .catch(function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al deshabilitar la dependencia.",
                    });
                });
        });
    });

    updateActionButtonsActivos();

    // ── Inactivos actions ──
    function updateActionButtonsInactivos() {
        var count = getSelectedIds(".dependencia-checkbox-inactivas").length;
        $("#btn-habilitar-dependencia").prop("disabled", count === 0);
    }

    $(document).on("change", ".dependencia-checkbox-inactivas", function () {
        if ($(this).prop("checked")) {
            $(".dependencia-checkbox-inactivas").not(this).prop("checked", false);
        }
        updateActionButtonsInactivos();
    });

    $(document).on(
        "click",
        "#tabla-dependencias-inactivas tbody tr",
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
                .find(".dependencia-checkbox-inactivas")
                .first();
            if (!checkbox.length) {
                return;
            }

            $(".dependencia-checkbox-inactivas")
                .not(checkbox)
                .prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtonsInactivos();
        },
    );

    tablaInactivos.on("draw.dt", function () {
        updateActionButtonsInactivos();
    });

    $("#btn-habilitar-dependencia").on("click", function () {
        var ids = getSelectedIds(".dependencia-checkbox-inactivas");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "La dependencia seleccionada será habilitada nuevamente.",
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

            var url = window.dependenciasRoutes.habilitar.replace(
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
                    Swal.fire({
                        icon: "success",
                        title: "Habilitado",
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    tablaActivos.DataTable().ajax.reload(null, false);
                    tablaInactivos.DataTable().ajax.reload(null, false);
                })
                .catch(function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al habilitar la dependencia.",
                    });
                });
        });
    });

    updateActionButtonsInactivos();
});
