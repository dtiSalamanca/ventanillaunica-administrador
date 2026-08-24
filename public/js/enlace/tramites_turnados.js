/**
 * Trámites Turnados - DataTables para listado con tabs (pendientes, atendidas, rechazadas)
 * Depende de: jQuery, DataTables, window.enlaceRoutes
 */
(function () {
    const routes = window.enlaceRoutes || {};

    if (!routes.tramitesTurnados) {
        console.warn("[TramitesTurnados] No se encontró la ruta del endpoint.");
        return;
    }

    function renderEstatus(data) {
        if (data === 3) {
            return '<span class="badge bg-info text-dark">Por pagar</span>';
        }
        if (data === 2) {
            return '<span class="badge bg-danger">Rechazada</span>';
        }
        if (data === 1) {
            return '<span class="badge bg-primary">Turnado</span>';
        }
        return '<span class="badge bg-warning text-dark">Pendiente</span>';
    }

    function renderAcciones(data) {
        var url = routes.tramitesTurnadosDetalles.replace("__ID__", data);
        return (
            '<a href="' +
            url +
            '" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>'
        );
    }

    function initTurnadosDataTable(tableId, filterStatus) {
        return $("#" + tableId).DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: routes.tramitesTurnados,
                type: "GET",
                dataSrc: function (json) {
                    return json.data.filter(function (item) {
                        return item.estatus_solicitud === filterStatus;
                    });
                },
            },
            columns: [
                {
                    data: "id_solicitud",
                    render: function (data) {
                        return data ? "#" + data : "—";
                    },
                },
                { data: "tramite" },
                { data: "dependencia" },
                { data: "ciudadano" },
                { data: "fecha_turnado" },
                {
                    data: "estatus_solicitud",
                    render: renderEstatus,
                },
                {
                    data: "id_turnado",
                    render: renderAcciones,
                    orderable: false,
                },
            ],
            order: [[4, "desc"]],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.12.1/i18n/es-MX.json",
            },
            pageLength: 25,
            responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: [0, 1] },
                { responsivePriority: 2, targets: [4] },
                { responsivePriority: 3, targets: [5] },
            ],
        });
    }

    // Pendientes = estatus 1 (turnados), Atendidas = estatus 3, Rechazadas = estatus 2
    var tablaPendientes = initTurnadosDataTable("tabla-turnados-pendientes", 1);
    var tablaAtendidas = initTurnadosDataTable("tabla-turnados-atendidas", 3);
    var tablaRechazadas = initTurnadosDataTable("tabla-turnados-rechazadas", 2);

    // Recargar la tabla activa al cambiar de tab
    $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        var target = $(e.target).attr("data-bs-target");
        if (target === "#pendientes") {
            tablaPendientes.ajax.reload(null, false);
        } else if (target === "#atendidas") {
            tablaAtendidas.ajax.reload(null, false);
        } else if (target === "#rechazadas") {
            tablaRechazadas.ajax.reload(null, false);
        }
    });

    // Recargar al mostrar la pestaña (por si hay cambios)
    document.addEventListener("visibilitychange", function () {
        if (!document.hidden) {
            tablaPendientes.ajax.reload(null, false);
        }
    });
})();
