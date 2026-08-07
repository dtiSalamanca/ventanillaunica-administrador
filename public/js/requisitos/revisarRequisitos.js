$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    var tablaAsignados = $("#tabla-requisitos-asignados");

    if (!tablaAsignados.length) {
        return;
    }

    var dt = tablaAsignados.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        ajax: {
            url: window.requisitosRoutes.asignados,
            type: "GET",
            dataType: "json",
            dataSrc: "",
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Este trámite no tiene requisitos asignados",
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
                    return (
                        '<input type="checkbox" class="requisito-checkbox" value="' +
                        row.id_requisito + '_' + row.tipo_documento +
                        '">'
                    );
                },
            },
            { data: "nombre_documento" },
            {
                data: "tipo_documento",
    className: "w-tipo",
    render: function (data) {
        switch (data) {
            case "d":
                return '<span class="badge bg-primary">Documento personal</span>';

            case "p":
                return '<span class="badge bg-success">Documento del predio</span>';

            default:
                return '<span class="badge bg-secondary">Desconocido</span>';
        }
    },
            },
        ],
    });

    // ── Selección de fila ──
    function getSelectedIds() {
        var ids = [];
        $(".requisito-checkbox:checked").each(function () {
            ids.push($(this).val());
        });
        return ids;
    }

    function updateActionButtons() {
        var count = getSelectedIds().length;
        $("#btn-quitar-requisito").prop("disabled", count === 0);
    }

    $(document).on("change", ".requisito-checkbox", function () {
        if ($(this).prop("checked")) {
            $(".requisito-checkbox").not(this).prop("checked", false);
        }
        updateActionButtons();
    });

    $(document).on("click", "#tabla-requisitos-asignados tbody tr", function (event) {
        var target = $(event.target);
        if (target.closest("input, button, a, label, select, textarea").length) {
            return;
        }
        var selectedRow = $(this);
        if (selectedRow.hasClass("child")) {
            selectedRow = selectedRow.prev();
        }
        var checkbox = selectedRow.find(".requisito-checkbox").first();
        if (!checkbox.length) {
            return;
        }
        $(".requisito-checkbox").not(checkbox).prop("checked", false);
        checkbox.prop("checked", true);
        updateActionButtons();
    });

    tablaAsignados.on("draw.dt", function () {
        updateActionButtons();
    });

    updateActionButtons();

    // ── Quitar requisito del trámite ──
    $("#btn-quitar-requisito").on("click", function () {
        var ids = getSelectedIds();
        if (ids.length === 0) {
            return;
        }

        Swal.fire({
            title: "¿Quitar requisito?",
            text: "El requisito se quitará de este trámite, pero permanecerá en el catálogo.",
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

            var url = window.requisitosRoutes.quitar.replace("__ID__", ids[0]);

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                        text: "Ocurrió un error al quitar el requisito.",
                    });
                });
        });
    });

    // ── Modal asignar: Select2 ──
    var $modal = $("#modalAsignarRequisitos");
    var $select = $("#select-requisitos");
    var $modalAlert = $("#modal-alert");

    // $modal.on("show.bs.modal", function () {
    //     $modalAlert.addClass("d-none").text("");

    //     // Destruir instancia previa de Select2
    //     if ($select.hasClass("select2-hidden-accessible")) {
    //         $select.select2("destroy");
    //     }

    //     $select.empty();

    //     $select.select2({
    //         theme: "bootstrap-5",
    //         language: "es",
    //         placeholder: "Buscar y seleccionar requisitos...",
    //         allowClear: true,
    //         width: "100%",
    //         dropdownParent: $modal,
    //         ajax: {
    //             url: window.requisitosRoutes.catalogo,
    //             dataType: "json",
    //             delay: 250,
    //             processResults: function (data) {
    //                 return {
    //                     results: data.map(function (item) {
    //                         return {
    //                             id: item.id_requisito,
    //                             text: item.nombre_requisito,
    //                         };
    //                     }),
    //                 };
    //             },
    //             cache: true,
    //         },
    //         minimumInputLength: 0,
    //     });
    // });

    const $selectPersonales = $("#select-personal");
    const $selectPredio = $("#select-requisitos");

$modal.on("show.bs.modal", function () {
    $modalAlert.addClass("d-none").text("");

    // Destruir instancias previas
    [$selectPersonales, $selectPredio].forEach(function ($select) {
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2("destroy");
        }
        $select.empty();
    });

    $.ajax({
        url: window.requisitosRoutes.catalogo,
        type: "GET",
        dataType: "json",
        success: function (response) {

            // ===========================
            // Documentos personales
            // ===========================
            response.docsPersonales.forEach(function (item) {
                $selectPersonales.append(
                    new Option(item.nombre_documento, item.id_documento)
                );
            });

            $selectPersonales.select2({
                theme: "bootstrap-5",
                language: "es",
                placeholder: "Seleccione documentos personales...",
                allowClear: true,
                width: "100%",
                dropdownParent: $modal
            });

            // ===========================
            // Documentos del predio
            // ===========================
            response.docsPredio.forEach(function (item) {
                $selectPredio.append(
                    new Option(item.nombre_documento, item.id_documento_predio)
                );
            });

            $selectPredio.select2({
                theme: "bootstrap-5",
                language: "es",
                placeholder: "Seleccione documentos del predio...",
                allowClear: true,
                width: "100%",
                dropdownParent: $modal
            });
        },
        error: function () {
            $modalAlert
                .removeClass("d-none")
                .text("No fue posible cargar el catálogo de documentos.");
        }
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
        var seleccionadosPersonales = $selectPersonales.val();
        var seleccionadosPredio = $selectPredio.val();
        
        if ((!seleccionadosPersonales || seleccionadosPersonales.length === 0) &&
            (!seleccionadosPredio || seleccionadosPredio.length === 0)) {
            $modalAlert
                .removeClass("d-none")
                .text("Debe seleccionar al menos un documento personal o del predio.");
            return;
        }
        console.log("Seleccionados personales:", seleccionadosPersonales);
        console.log("Seleccionados predio:", seleccionadosPredio);
        
        $modalAlert.addClass("d-none").text("");
        fetch(window.requisitosRoutes.asignarE, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ 
                documentos_personales: seleccionadosPersonales,
                documentos_predio: seleccionadosPredio
            }),
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { status: response.status, data: data };
            });
        })
        .then(function (result) {
            if (result.status === 422) {
                var errors = result.data.errors || {};
                var firstError =
                    errors.documentos_personales || errors.documentos_predio
                        ? (errors.documentos_personales ? errors.documentos_personales[0] : errors.documentos_predio[0])
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
        // var seleccionados = $select.val();

        // if (!seleccionados || seleccionados.length === 0) {
        //     $modalAlert
        //         .removeClass("d-none")
        //         .text("Debe seleccionar al menos un requisito.");
        //     return;
        // }

        // $modalAlert.addClass("d-none").text("");

        // fetch(window.requisitosRoutes.asignar, {
        //     method: "POST",
        //     headers: {
        //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        //         "Content-Type": "application/json",
        //         Accept: "application/json",
        //     },
        //     body: JSON.stringify({ requisitos: seleccionados }),
        // })
        //     .then(function (response) {
        //         return response.json().then(function (data) {
        //             return { status: response.status, data: data };
        //         });
        //     })
        //     .then(function (result) {
        //         if (result.status === 422) {
        //             var errors = result.data.errors || {};
        //             var firstError =
        //                 errors.requisitos
        //                     ? errors.requisitos[0]
        //                     : result.data.message || "Error de validación.";
        //             $modalAlert.removeClass("d-none").text(firstError);
        //             return;
        //         }

        //         $modal.modal("hide");
        //         Swal.fire({
        //             icon: "success",
        //             title: "Asignado",
        //             text: result.data.message,
        //             timer: 2000,
        //             showConfirmButton: false,
        //         });
        //         dt.ajax.reload(null, false);
        //     })
        //     .catch(function () {
        //         $modalAlert
        //             .removeClass("d-none")
        //             .text("Ocurrió un error inesperado. Intente de nuevo.");
        //     });
    });
});
