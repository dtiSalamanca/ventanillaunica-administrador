$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // ---- Elementos DOM ----
    const tablaActivos = $("#tabla-usuarios-activos");
    const tablaInactivos = $("#tabla-usuarios-inactivos");
    const botonRecargar = $("#btn-recargar-usuarios");
    const selectionCount = $("#selection-count");
    const btnAsignarDependencia = $("#btn-asignar-dependencia");
    const modalAsignar = $("#modalAsignarDependencia");
    const selectDependencia = $("#select-dependencia");
    const formAsignar = $("#form-asignar-dependencia");
    const modalUserCount = $("#modal-user-count");
    const btnGuardar = $("#btn-guardar-dependencia");

    if (!tablaActivos.length || !tablaInactivos.length) {
        return;
    }

    // ---- Estado de selección ----
    const usuariosSeleccionados = new Set();

    // ---- Utilerías ----
    function escapeHtml(value) {
        return $("<div>").text(value).html();
    }

    // ---- Idioma DataTables ----
    const language = {
        processing: "Procesando...",
        lengthMenu: "Mostrar _MENU_ registros",
        zeroRecords: "No se encontraron resultados",
        emptyTable: "Ningún usuario disponible en esta tabla",
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
                const checked = usuariosSeleccionados.has(row.username)
                    ? "checked"
                    : "";
                return (
                    '<input type="checkbox" class="usuario-checkbox" value="' +
                    escapeHtml(row.username) +
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
            data: "username",
            className: "w-usuario",
            render: $.fn.dataTable.render.text(),
        },
        {
            data: "nombre_completo",
            className: "w-nombre",
            render: $.fn.dataTable.render.text(),
        },
        {
            data: "rol",
            className: "w-rol",
            render: function (data) {
                return (
                    '<span class="usuario-badge usuario-badge-rol">' +
                    escapeHtml(data || "Sin rol") +
                    "</span>"
                );
            },
        },
    ];

    const columnsActivos = [checkboxColumn].concat(dataColumns);
    const columnsInactivos = [checkboxColumn].concat(dataColumns);

    const dataTableOptions = {
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[2, "asc"]],
        language: language,
        data: [],
    };

    const dataTableActivos = tablaActivos.DataTable(
        $.extend({}, dataTableOptions, {
            columns: columnsActivos,
        }),
    );

    const dataTableInactivos = tablaInactivos.DataTable(
        $.extend({}, dataTableOptions, {
            columns: columnsInactivos,
            language: $.extend({}, language, {
                emptyTable: "Ningún usuario inactivo disponible",
            }),
        }),
    );

    // ---- Manejo de selección ----
    function actualizarSeleccion() {
        const total = usuariosSeleccionados.size;
        selectionCount.text(total + " usuario(s) seleccionado(s)");
        btnAsignarDependencia.prop("disabled", total === 0);
    }

    function sincronizarCheckboxes(tablaId) {
        const table = $("#" + tablaId).DataTable();
        table.rows().every(function () {
            const row = this.data();
            if (!row) return;
            const rowNode = this.node();
            if (!rowNode) return;
            const checkbox = $(rowNode).find(".usuario-checkbox");
            if (checkbox.length) {
                checkbox.prop(
                    "checked",
                    usuariosSeleccionados.has(row.username),
                );
            }
        });
    }

    $(document).on("change", ".usuario-checkbox", function () {
        const username = $(this).val();
        if ($(this).is(":checked")) {
            usuariosSeleccionados.add(username);
        } else {
            usuariosSeleccionados.delete(username);
        }
        actualizarSeleccion();
    });

    // Al redibujar DataTable (paginación, búsqueda, etc.), restaurar checkboxes
    tablaActivos.on("draw.dt", function () {
        sincronizarCheckboxes("tabla-usuarios-activos");
    });
    tablaInactivos.on("draw.dt", function () {
        sincronizarCheckboxes("tabla-usuarios-inactivos");
    });

    // ---- Carga de datos ----
    function setLoading(isLoading) {
        botonRecargar.prop("disabled", isLoading);
        botonRecargar.toggleClass("is-loading", isLoading);
        botonRecargar.find("i").toggleClass("fa-spin", isLoading);
    }

    function actualizarTablas(usuarios) {
        const activos = usuarios.filter(function (usuario) {
            return usuario.activo === true;
        });
        const inactivos = usuarios.filter(function (usuario) {
            return usuario.activo !== true;
        });

        usuariosSeleccionados.clear();
        actualizarSeleccion();

        dataTableActivos.clear().rows.add(activos).draw();
        dataTableInactivos.clear().rows.add(inactivos).draw();
    }

    function cargarUsuarios() {
        setLoading(true);

        $.ajax({
            url: window.usuariosRoutes.index,
            type: "GET",
            dataType: "json",
        })
            .done(function (usuarios) {
                actualizarTablas(Array.isArray(usuarios) ? usuarios : []);
            })
            .fail(function (xhr) {
                var message =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Ocurrió un error al consultar los usuarios de AD.";

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
        cargarUsuarios();
    });

    // ---- Modal: cargar dependencias ----
    function cargarDependencias() {
        return $.ajax({
            url: window.usuariosRoutes.dependenciasActivas,
            type: "GET",
            dataType: "json",
        }).done(function (dependencias) {
            selectDependencia.find("option:not(:first)").remove();
            if (Array.isArray(dependencias)) {
                dependencias.forEach(function (dep) {
                    selectDependencia.append(
                        '<option value="' +
                            dep.id_dependencia +
                            '">' +
                            escapeHtml(dep.nombre_dependencia) +
                            "</option>",
                    );
                });
            }
        });
    }

    btnAsignarDependencia.on("click", function () {
        var count = usuariosSeleccionados.size;
        if (count === 0) return;

        modalUserCount.text(count);

        selectDependencia.val("").prop("disabled", true);
        btnGuardar.prop("disabled", true);

        cargarDependencias().always(function () {
            selectDependencia.prop("disabled", false);
            btnGuardar.prop("disabled", false);
        });

        modalAsignar.modal("show");
    });

    // ---- Modal: guardar asignación ----
    formAsignar.on("submit", function (e) {
        e.preventDefault();

        var fkDependencia = selectDependencia.val();
        if (!fkDependencia) {
            Swal.fire({
                icon: "warning",
                title: "Campo requerido",
                text: "Selecciona una dependencia.",
            });
            return;
        }

        var usuarios = Array.from(usuariosSeleccionados);

        btnGuardar.prop("disabled", true);

        $.ajax({
            url: window.usuariosRoutes.asignarDependencia,
            type: "POST",
            dataType: "json",
            data: {
                usuarios: usuarios,
                fk_dependencia: fkDependencia,
            },
        })
            .done(function (response) {
                modalAsignar.modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "¡Asignado!",
                    text: response.message,
                    timer: 3000,
                    timerProgressBar: true,
                });
            })
            .fail(function (xhr) {
                var message = "Ocurrió un error al asignar la dependencia.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var parts = [];
                    for (var key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            parts.push(errors[key].join(", "));
                        }
                    }
                    if (parts.length) {
                        message = parts.join("\n");
                    }
                }
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: message,
                });
            })
            .always(function () {
                btnGuardar.prop("disabled", false);
            });
    });

    // Limpiar selección al cerrar modal
    modalAsignar.on("hidden.bs.modal", function () {
        selectDependencia.val("");
    });

    // ---- Inicializar ----
    cargarUsuarios();
});
