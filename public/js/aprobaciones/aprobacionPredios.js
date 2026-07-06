$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    var tabs = {
        pendientes: {
            input: "#pendientes-search-input",
            clear: "#pendientes-search-clear",
            resultado: "#pendientes-resultado",
            queryParam: "pendientesQ",
            pageParam: "pendientesPage",
            page: 1,
        },
        "sin-pendientes": {
            input: "#sin-pendientes-search-input",
            clear: "#sin-pendientes-search-clear",
            resultado: "#sin-pendientes-resultado",
            queryParam: "sinPendientesQ",
            pageParam: "sinPendientesPage",
            page: 1,
        },
    };

    var debounceTimers = {};

    function obtenerAcordeonesAbiertos($resultado) {
        var ids = [];
        $resultado.find(".accordion-collapse.show").each(function () {
            ids.push(this.id);
        });
        return ids;
    }

    function reabrirAcordeones($resultado, ids) {
        ids.forEach(function (id) {
            var $collapse = $resultado.find("#" + id);
            if ($collapse.length === 0) {
                return;
            }

            $collapse.addClass("show");
            $resultado
                .find('[data-bs-target="#' + id + '"]')
                .removeClass("collapsed")
                .attr("aria-expanded", "true");
        });
    }

    function cargarGrid(tab, page) {
        var config = tabs[tab];
        config.page = page || 1;

        var params = new URLSearchParams();
        params.set("tab", tab);
        params.set(config.queryParam, $(config.input).val() || "");
        params.set(config.pageParam, config.page);

        var $resultado = $(config.resultado);
        var idsAbiertos = obtenerAcordeonesAbiertos($resultado);
        $resultado.addClass("is-loading");

        fetch(window.aprobacionPrediosRoutes.buscar + "?" + params.toString(), {
            headers: { Accept: "application/json" },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                $resultado.html(data.html);
                reabrirAcordeones($resultado, idsAbiertos);
            })
            .catch(function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo cargar la información.",
                });
            })
            .finally(function () {
                $resultado.removeClass("is-loading");
            });
    }

    function debounceCargarGrid(tab) {
        clearTimeout(debounceTimers[tab]);
        debounceTimers[tab] = setTimeout(function () {
            cargarGrid(tab, 1);
        }, 400);
    }

    Object.keys(tabs).forEach(function (tab) {
        var config = tabs[tab];

        $(document).on("input", config.input, function () {
            $(config.clear).toggle(Boolean($(this).val()));
            debounceCargarGrid(tab);
        });

        $(document).on("keydown", config.input, function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                clearTimeout(debounceTimers[tab]);
                cargarGrid(tab, 1);
            }
        });

        $(document).on("click", config.clear, function () {
            $(config.input).val("");
            $(this).hide();
            cargarGrid(tab, 1);
        });
    });

    // Los enlaces de paginación deben interceptarse en fase de captura: el
    // listener global de la cortinilla en layouts/admin.js se registra antes
    // que este script y revisa `event.defaultPrevented` en fase de burbuja,
    // por lo que un preventDefault() tardío no evita que muestre la cortinilla
    // (y como aquí nunca hay una navegación real, la cortinilla no vuelve a
    // ocultarse). Capturando el clic antes de que llegue a esa fase evita el problema.
    document.addEventListener(
        "click",
        function (event) {
            var anchor = event.target.closest(
                "#pendientes-resultado .pagination a, #sin-pendientes-resultado .pagination a",
            );
            if (!anchor) {
                return;
            }

            event.preventDefault();

            var tab = anchor.closest("#pendientes-resultado") ? "pendientes" : "sin-pendientes";
            var config = tabs[tab];
            var url = new URL(anchor.href, window.location.origin);
            var page = url.searchParams.get(config.pageParam) || 1;
            cargarGrid(tab, page);
        },
        true,
    );

    function enviarRevision(url, successTitle, errorText, loadingText) {
        Swal.fire({
            title: loadingText || "Procesando...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function () {
                Swal.showLoading();
            },
        });

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
                    title: successTitle,
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(function () {
                    cargarGrid("pendientes", tabs.pendientes.page);
                    cargarGrid("sin-pendientes", tabs["sin-pendientes"].page);
                });
            })
            .catch(function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorText,
                });
            });
    }

    function confirmarRevision(options) {
        Swal.fire({
            title: "¿Está seguro?",
            text: options.text,
            icon: options.icon,
            showCancelButton: true,
            confirmButtonColor: options.confirmButtonColor,
            cancelButtonColor: "#6c757d",
            confirmButtonText: options.confirmButtonText,
            cancelButtonText: "Cancelar",
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            enviarRevision(options.url, options.successTitle, options.errorText, options.loadingText);
        });
    }

    $(document).on("click", ".btn-aprobar-predio", function () {
        var id = $(this).data("id");
        var url = window.aprobacionPrediosRoutes.aprobarPredio.replace("__ID__", id);

        confirmarRevision({
            url: url,
            text: "El predio seleccionado será aprobado.",
            icon: "question",
            confirmButtonColor: "#10b981",
            confirmButtonText: "Sí, aprobar",
            successTitle: "Aprobado",
            errorText: "Ocurrió un error al procesar el predio.",
            loadingText: "Aprobando predio y enviando notificación por correo…",
        });
    });

    $(document).on("click", ".btn-rechazar-predio", function () {
        var id = $(this).data("id");
        var url = window.aprobacionPrediosRoutes.rechazarPredio.replace("__ID__", id);

        confirmarRevision({
            url: url,
            text: "El predio seleccionado será rechazado.",
            icon: "warning",
            confirmButtonColor: "#d33",
            confirmButtonText: "Sí, rechazar",
            successTitle: "Rechazado",
            errorText: "Ocurrió un error al procesar el predio.",
            loadingText: "Rechazando predio y enviando notificación por correo…",
        });
    });

    $(document).on("click", ".btn-aprobar-documento-predio", function () {
        var id = $(this).data("id");
        var url = window.aprobacionPrediosRoutes.aprobarDocumento.replace("__ID__", id);

        confirmarRevision({
            url: url,
            text: "El documento de predio seleccionado será aprobado.",
            icon: "question",
            confirmButtonColor: "#10b981",
            confirmButtonText: "Sí, aprobar",
            successTitle: "Aprobado",
            errorText: "Ocurrió un error al procesar el documento de predio.",
            loadingText: "Procesando documento…",
        });
    });

    $(document).on("click", ".btn-rechazar-documento-predio", function () {
        var id = $(this).data("id");
        var url = window.aprobacionPrediosRoutes.rechazarDocumento.replace("__ID__", id);

        confirmarRevision({
            url: url,
            text: "El documento de predio seleccionado será rechazado.",
            icon: "warning",
            confirmButtonColor: "#d33",
            confirmButtonText: "Sí, rechazar",
            successTitle: "Rechazado",
            errorText: "Ocurrió un error al procesar el documento de predio.",
            loadingText: "Procesando documento…",
        });
    });
});
