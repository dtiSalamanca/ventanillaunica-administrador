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

    // ---- Elementos DOM ----
    const tablaActivos = $("#tabla-usuarios-activos");
    // const tablaInactivos = $("#tabla-usuarios-inactivos"); // tab inactivos comentado
    const botonRecargar = $("#btn-recargar-usuarios");
    const selectionCount = $("#selection-count");
    const btnAsignarDependencia = $("#btn-asignar-dependencia");
    const modalAsignar = $("#modalAsignarDependencia");
    const selectDependencia = $("#select-dependencia");
    const formAsignar = $("#form-asignar-dependencia");
    const modalUserCount = $("#modal-user-count");
    const btnGuardar = $("#btn-guardar-dependencia");

    if (!tablaActivos.length) {
        return;
    }

    // ---- Estado de selección ----
    const usuariosSeleccionados = new Set();

    // Cache de usuarios cargados para mostrar nombres en el mensaje de asignación
    const usuariosCache = {};

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

    // Solo los usuarios con rol Enlace pueden tener dependencia asignada.
    // Se compara por el texto del rol (la API de usuarios puede devolver un
    // rol_id distinto al del login, así que el rol es la fuente confiable).
    function esEnlace(row) {
        return String(row.rol || "").toLowerCase() === "enlace";
    }

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
                const bloqueado = esEnlace(row)
                    ? ""
                    : ' disabled title="Solo usuarios con rol Enlace"';
                return (
                    '<input type="checkbox" class="usuario-checkbox" value="' +
                    escapeHtml(row.username) +
                    '" ' +
                    checked +
                    bloqueado +
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
        {
            data: "dependencia",
            className: "w-dependencia",
            render: function (data) {
                if (!data) {
                    return (
                        '<span class="usuario-sin-dependencia">' +
                        '<i class="fas fa-building-circle-exclamation me-1"></i>Sin asignar</span>'
                    );
                }
                return (
                    '<span class="usuario-badge usuario-badge-dependencia">' +
                    '<i class="fas fa-building me-1"></i>' +
                    escapeHtml(data) +
                    "</span>"
                );
            },
        },
    ];

    const columnsActivos = [checkboxColumn].concat(dataColumns);
    // const columnsInactivos = [checkboxColumn].concat(dataColumns); // tab inactivos comentado

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

    // Tab de inactivos comentado temporalmente
    // const dataTableInactivos = tablaInactivos.DataTable(
    //     $.extend({}, dataTableOptions, {
    //         columns: columnsInactivos,
    //         language: $.extend({}, language, {
    //             emptyTable: "Ningún usuario inactivo disponible",
    //         }),
    //     }),
    // );

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
    // Tab de inactivos comentado temporalmente
    // tablaInactivos.on("draw.dt", function () {
    //     sincronizarCheckboxes("tabla-usuarios-inactivos");
    // });

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
        // const inactivos = usuarios.filter(function (usuario) {
        //     return usuario.activo !== true;
        // }); // tab inactivos comentado

        // Actualizar caché de usuarios para el mensaje de asignación
        Object.keys(usuariosCache).forEach(function (key) {
            delete usuariosCache[key];
        });
        activos.forEach(function (usuario) {
            usuariosCache[usuario.username] = usuario;
        });

        usuariosSeleccionados.clear();
        actualizarSeleccion();

        dataTableActivos.clear().rows.add(activos).draw();
        // dataTableInactivos.clear().rows.add(inactivos).draw(); // tab inactivos comentado
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
        var nombresUsuarios = usuarios.map(function (username) {
            var usuario = usuariosCache[username];
            return (usuario && usuario.nombre_completo) || username;
        });

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
                mostrarAlerta(
                    "Dependencia asignada a: " + nombresUsuarios.join(", "),
                    "success",
                );
                // Recargar usuarios para reflejar el cambio de dependencia sin refrescar
                cargarUsuarios();
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
