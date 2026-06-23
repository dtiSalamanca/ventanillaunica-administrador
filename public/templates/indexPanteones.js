$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    const tablaActivos = $("#tabla-panteones-activos");
    const tablaInactivos = $("#tabla-panteones-inactivos");

    if (!tablaActivos.length || !tablaInactivos.length) {
        return;
    }

    tablaActivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.panteonesRoutes.activos,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún panteón disponible en esta tabla",
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
        },
        columns: [
            {
                data: null,
                className: "w-checkbox",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="panteon-checkbox-activos" value="' + row.id_panteon + '" data-id="' + row.id_panteon + '">';
                },
            },
            { data: "nombre_panteon", className: "w-panteon" },
            { data: "direccion_panteon", className: "w-descripcion" },
        ],
    });

    tablaInactivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.panteonesRoutes.inactivos,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún panteón inactivo disponible",
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
        },
        columns: [
            {
                data: null,
                className: "w-checkbox",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="panteon-checkbox-inactivos" value="' + row.id_panteon + '" data-id="' + row.id_panteon + '">';
                },
            },
            { data: "nombre_panteon", className: "w-panteon" },
            { data: "direccion_panteon", className: "w-descripcion" },
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
        var count = getSelectedIds(".panteon-checkbox-activos").length;
        $("#btn-editar-panteon-activos").prop("disabled", count !== 1);
        $("#btn-deshabilitar-panteon").prop("disabled", count === 0);
    }

    $(document).on("change", ".panteon-checkbox-activos", function () {
        if ($(this).prop("checked")) {
            $(".panteon-checkbox-activos").not(this).prop("checked", false);
        }
        updateActionButtonsActivos();
    });

    $(document).on("click", "#tabla-panteones-activos tbody tr", function (event) {
        var target = $(event.target);
        if (target.closest("input, button, a, label, select, textarea").length) {
            return;
        }

        var selectedRow = $(this);
        if (selectedRow.hasClass("child")) {
            selectedRow = selectedRow.prev();
        }

        var checkbox = selectedRow.find(".panteon-checkbox-activos").first();
        if (!checkbox.length) {
            return;
        }

        $(".panteon-checkbox-activos").not(checkbox).prop("checked", false);
        checkbox.prop("checked", true);
        updateActionButtonsActivos();
    });

    tablaActivos.on("draw.dt", function () {
        updateActionButtonsActivos();
    });

    $("#btn-editar-panteon-activos").on("click", function () {
        var ids = getSelectedIds(".panteon-checkbox-activos");
        if (ids.length === 1) {
            var url = window.panteonesRoutes.editar.replace("__ID__", ids[0]);
            window.location.href = url;
        }
    });

    $("#btn-deshabilitar-panteon").on("click", function () {
        var ids = getSelectedIds(".panteon-checkbox-activos");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "El panteón seleccionado será deshabilitado.",
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

            var url = window.panteonesRoutes.deshabilitar.replace("__ID__", ids[0]);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    "Content-Type": "application/json",
                    "Accept": "application/json",
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
                        text: "Ocurrió un error al deshabilitar el panteón.",
                    });
                });
        });
    });

    updateActionButtonsActivos();

    // ── Inactivos actions ──
    function updateActionButtonsInactivos() {
        var count = getSelectedIds(".panteon-checkbox-inactivos").length;
        $("#btn-habilitar-panteon").prop("disabled", count === 0);
    }

    $(document).on("change", ".panteon-checkbox-inactivos", function () {
        if ($(this).prop("checked")) {
            $(".panteon-checkbox-inactivos").not(this).prop("checked", false);
        }
        updateActionButtonsInactivos();
    });

    $(document).on("click", "#tabla-panteones-inactivos tbody tr", function (event) {
        var target = $(event.target);
        if (target.closest("input, button, a, label, select, textarea").length) {
            return;
        }

        var selectedRow = $(this);
        if (selectedRow.hasClass("child")) {
            selectedRow = selectedRow.prev();
        }

        var checkbox = selectedRow.find(".panteon-checkbox-inactivos").first();
        if (!checkbox.length) {
            return;
        }

        $(".panteon-checkbox-inactivos").not(checkbox).prop("checked", false);
        checkbox.prop("checked", true);
        updateActionButtonsInactivos();
    });

    tablaInactivos.on("draw.dt", function () {
        updateActionButtonsInactivos();
    });

    $("#btn-habilitar-panteon").on("click", function () {
        var ids = getSelectedIds(".panteon-checkbox-inactivos");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "El panteón seleccionado será habilitado nuevamente.",
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

            var url = window.panteonesRoutes.habilitar.replace("__ID__", ids[0]);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    "Content-Type": "application/json",
                    "Accept": "application/json",
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
                        text: "Ocurrió un error al habilitar el panteón.",
                    });
                });
        });
    });

    updateActionButtonsInactivos();
});
