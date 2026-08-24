/**
 * Reporteador - DataTables para el reporte de solicitudes.
 * Depende de: jQuery, DataTables, window.reporteadorRoutes
 */
$(document).ready(function () {
    var tabla = $("#tabla-reporteador-solicitudes");

    if (
        !tabla.length ||
        !window.reporteadorRoutes ||
        !window.reporteadorRoutes.solicitudes
    ) {
        return;
    }

    function renderEstatus(estatus) {
        switch (estatus) {
            case 0:
                return '<span class="badge bg-warning text-dark">Pendiente</span>';
            case 1:
                return '<span class="badge bg-success">Turnada</span>';
            case 2:
                return '<span class="badge bg-danger">Rechazada</span>';
            case 3:
                return '<span class="badge bg-info text-dark">Por pagar</span>';
            case 4:
                return '<span class="badge bg-primary">Completado</span>';
            default:
                return '<span class="badge bg-secondary">Desconocido</span>';
        }
    }

    tabla.DataTable({
        processing: true,
        responsive: true,
        autoWidth: false,
        order: [[0, "desc"]],
        ajax: {
            url: window.reporteadorRoutes.solicitudes,
            type: "GET",
            dataType: "json",
            dataSrc: "",
            data: function (d) {
                d.fk_dependencia = $("#filtro-dependencia").val() || "";
                d.estatus = $("#filtro-estatus").val() || "";
                d.fecha_desde = $("#filtro-fecha-desde").val() || "";
                d.fecha_hasta = $("#filtro-fecha-hasta").val() || "";
            },
        },
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay solicitudes registradas",
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
            { data: "id_solicitud" },
            { data: "nombre_tramite" },
            { data: "nombre_dependencia" },
            { data: "nombre_usuario" },
            {
                data: "fecha_solicitud",
                render: function (data, type, row) {
                    if (!data) return "—";

                    // El valor viene de la BD ya en hora de México, así que se
                    // formatea directamente sin conversión de zona horaria.
                    var partes = data.split(" ");
                    if (partes.length < 2) return data;

                    var fecha = partes[0].split("-");
                    var hora = partes[1].split(":").map(Number);
                    var horas12 = hora[0] % 12 === 0 ? 12 : hora[0] % 12;
                    var periodo = hora[0] < 12 ? "a.m." : "p.m.";
                    var pad = function (n) {
                        return String(n).padStart(2, "0");
                    };

                    return (
                        fecha[2] +
                        "-" +
                        fecha[1] +
                        "-" +
                        fecha[0] +
                        " " +
                        pad(horas12) +
                        ":" +
                        pad(hora[1]) +
                        " " +
                        periodo
                    );
                },
            },
            {
                data: "estatus_mostrado",
                render: renderEstatus,
            },
        ],
    });

    // Recargar la tabla al aplicar filtros
    $("#btn-aplicar-filtros").on("click", function () {
        tabla.DataTable().ajax.reload();
    });
});
