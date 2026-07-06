$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const tablaActivos = $("#tabla-usuarios-activos");
    const tablaInactivos = $("#tabla-usuarios-inactivos");
    const botonRecargar = $("#btn-recargar-usuarios");

    if (!tablaActivos.length || !tablaInactivos.length) {
        return;
    }

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

    const columns = [
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
                return '<span class="usuario-badge usuario-badge-rol">' + escapeHtml(data || "Sin rol") + "</span>";
            },
        },
    ];

    const dataTableOptions = {
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[1, "asc"]],
        language: language,
        columns: columns,
        data: [],
    };

    const dataTableActivos = tablaActivos.DataTable(dataTableOptions);
    const dataTableInactivos = tablaInactivos.DataTable({
        ...dataTableOptions,
        language: {
            ...language,
            emptyTable: "Ningún usuario inactivo disponible",
        },
    });

    function escapeHtml(value) {
        return $("<div>").text(value).html();
    }

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
                const message =
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

    cargarUsuarios();
});
