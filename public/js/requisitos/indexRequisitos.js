$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const tablaActivos = $("#tabla-requisitos-activos");
    const tablaInactivos = $("#tabla-requisitos-inactivos");

    if (!tablaActivos.length || !tablaInactivos.length) {
        return;
    }

    tablaActivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.requisitosRoutes.activas,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún requisito disponible en esta tabla",
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
                        '<input type="checkbox" class="requisito-checkbox-activos" value="' +
                        row.id_requisito +
                        '" data-id="' +
                        row.id_requisito +
                        '">'
                    );
                },
            },
            { data: "nombre", className: "w-requisito" },
        ],
    });

    tablaInactivos.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.requisitosRoutes.inactivas,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún requisito inactivo disponible",
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
                        '<input type="checkbox" class="requisito-checkbox-inactivos" value="' +
                        row.id_requisito +
                        '" data-id="' +
                        row.id_requisito +
                        '">'
                    );
                },
            },
            { data: "nombre", className: "w-requisito" },
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
        var count = getSelectedIds(".requisito-checkbox-activos").length;
        $("#btn-editar-requisito-activos").prop("disabled", count !== 1);
        $("#btn-deshabilitar-requisito").prop("disabled", count === 0);
    }

    $(document).on("change", ".requisito-checkbox-activos", function () {
        if ($(this).prop("checked")) {
            $(".requisito-checkbox-activos").not(this).prop("checked", false);
        }
        updateActionButtonsActivos();
    });

    $(document).on(
        "click",
        "#tabla-requisitos-activos tbody tr",
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
                .find(".requisito-checkbox-activos")
                .first();
            if (!checkbox.length) {
                return;
            }

            $(".requisito-checkbox-activos").not(checkbox).prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtonsActivos();
        },
    );

    tablaActivos.on("draw.dt", function () {
        updateActionButtonsActivos();
    });

    $("#btn-editar-requisito-activos").on("click", function () {
        var ids = getSelectedIds(".requisito-checkbox-activos");
        if (ids.length === 1) {
            var url = window.requisitosRoutes.editar.replace("__ID__", ids[0]);
            window.location.href = url;
        }
    });

    $("#btn-deshabilitar-requisito").on("click", function () {
        var ids = getSelectedIds(".requisito-checkbox-activos");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "El requisito seleccionado será deshabilitado.",
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

            var url = window.requisitosRoutes.deshabilitar.replace(
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
                        text: "Ocurrió un error al deshabilitar el requisito.",
                    });
                });
        });
    });

    updateActionButtonsActivos();

    // ── Inactivos actions ──
    function updateActionButtonsInactivos() {
        var count = getSelectedIds(".requisito-checkbox-inactivos").length;
        $("#btn-habilitar-requisito").prop("disabled", count === 0);
    }

    $(document).on("change", ".requisito-checkbox-inactivos", function () {
        if ($(this).prop("checked")) {
            $(".requisito-checkbox-inactivos").not(this).prop("checked", false);
        }
        updateActionButtonsInactivos();
    });

    $(document).on(
        "click",
        "#tabla-requisitos-inactivos tbody tr",
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
                .find(".requisito-checkbox-inactivos")
                .first();
            if (!checkbox.length) {
                return;
            }

            $(".requisito-checkbox-inactivos")
                .not(checkbox)
                .prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtonsInactivos();
        },
    );

    tablaInactivos.on("draw.dt", function () {
        updateActionButtonsInactivos();
    });

    $("#btn-habilitar-requisito").on("click", function () {
        var ids = getSelectedIds(".requisito-checkbox-inactivos");
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Está seguro?",
            text: "El requisito seleccionado será habilitado nuevamente.",
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

            var url = window.requisitosRoutes.habilitar.replace(
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
                        text: "Ocurrió un error al habilitar el requisito.",
                    });
                });
        });
    });

    updateActionButtonsInactivos();
});
