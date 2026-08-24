$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    var tablaAsignados = $("#tabla-prerequisitos-asignados");

    if (!tablaAsignados.length) {
        return;
    }

    var dt = tablaAsignados.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.prerequisitosRoutes.asignados,
            type: "GET",
            dataType: "json",
            dataSrc: "",
            cache: false,
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Este trámite no tiene prerequisitos asignados",
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
                        '<input type="checkbox" class="prerequisito-checkbox" value="' +
                        row.id_tramite +
                        '">'
                    );
                },
            },
            {
                data: "nombre_tramite",
                render: function (data) {
                    return (
                        data +
                        ' <span class="badge-tramite-tag"><i class="fas fa-file-lines me-1"></i>Trámite</span>'
                    );
                },
            },
            {
                data: "estatus_tramite",
                className: "w-estado",
                orderable: false,
                searchable: false,
                render: function (data) {
                    return data
                        ? '<span class="badge-activo"><i class="fas fa-circle-check me-1"></i>Activo</span>'
                        : '<span class="badge-inactivo"><i class="fas fa-circle-xmark me-1"></i>Inactivo</span>';
                },
            },
        ],
    });

    // ── Selección de fila ──
    function getSelectedIds() {
        var ids = [];
        $(".prerequisito-checkbox:checked").each(function () {
            ids.push($(this).val());
        });
        return ids;
    }

    function updateActionButtons() {
        var count = getSelectedIds().length;
        $("#btn-quitar-prerequisito").prop("disabled", count === 0);
    }

    $(document).on("change", ".prerequisito-checkbox", function () {
        if ($(this).prop("checked")) {
            $(".prerequisito-checkbox").not(this).prop("checked", false);
        }
        updateActionButtons();
    });

    $(document).on(
        "click",
        "#tabla-prerequisitos-asignados tbody tr",
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
            var checkbox = selectedRow.find(".prerequisito-checkbox").first();
            if (!checkbox.length) {
                return;
            }
            $(".prerequisito-checkbox").not(checkbox).prop("checked", false);
            checkbox.prop("checked", true);
            updateActionButtons();
        },
    );

    tablaAsignados.on("draw.dt", function () {
        updateActionButtons();
    });

    updateActionButtons();

    // ── Quitar prerequisito ──
    $("#btn-quitar-prerequisito").on("click", function () {
        var ids = getSelectedIds();
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Quitar prerequisito?",
            text: "El trámite ya no requerirá este prerequisito para ser solicitado.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, quitar",
            cancelButtonText: "Cancelar",
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            var url = window.prerequisitosRoutes.quitar.replace(
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
                        title: "Quitado",
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    dt.ajax.reload(null, false);
                })
                .catch(function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un error al quitar el prerequisito.",
                    });
                });
        });
    });

    // ── Modal asignar: Select2 ──
    var $modal = $("#modalAsignarPrerequisitos");
    var $select = $("#select-prerequisitos");
    var $modalAlert = $("#modal-alert");

    $modal.on("show.bs.modal", function () {
        $modalAlert.addClass("d-none").text("");

        // Destruir instancia previa de Select2
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2("destroy");
        }

        $select.empty();

        $select.select2({
            theme: "bootstrap-5",
            language: "es",
            placeholder: "Buscar y seleccionar trámites...",
            allowClear: true,
            width: "100%",
            dropdownParent: $modal,
            ajax: {
                url: window.prerequisitosRoutes.catalogo,
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id_tramite,
                                text: item.nombre_tramite,
                            };
                        }),
                    };
                },
                cache: true,
            },
            minimumInputLength: 0,
        });
    });

    $modal.on("hidden.bs.modal", function () {
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2("destroy");
        }
        $select.empty();
        $modalAlert.addClass("d-none").text("");
    });

    // ── Guardar asignación ──
    $("#btn-guardar-asignacion").on("click", function () {
        var seleccionados = $select.val();

        if (!seleccionados || seleccionados.length === 0) {
            $modalAlert
                .removeClass("d-none")
                .text("Debe seleccionar al menos un trámite.");
            return;
        }

        $modalAlert.addClass("d-none").text("");

        fetch(window.prerequisitosRoutes.asignar, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ prerequisitos: seleccionados }),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { status: response.status, data: data };
                });
            })
            .then(function (result) {
                if (result.status === 422) {
                    var errors = result.data.errors || {};
                    var firstError = errors.prerequisitos
                        ? errors.prerequisitos[0]
                        : result.data.message || "Error de validación.";
                    $modalAlert.removeClass("d-none").text(firstError);
                    return;
                }

                $modal.modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "Asignado",
                    text: result.data.message,
                    timer: 2000,
                    showConfirmButton: false,
                });
                dt.ajax.reload(null, false);
            })
            .catch(function () {
                $modalAlert
                    .removeClass("d-none")
                    .text("Ocurrió un error inesperado. Intente de nuevo.");
            });
    });
});
