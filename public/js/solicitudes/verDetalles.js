$(document).ready(function () {
    // Elementos del DOM
    const $btnAprobar = $("#btnAprobar");
    const $btnRechazar = $("#btnRechazar");
    const $turnadoPanel = $("#turnadoPanel");
    const $rechazoPanel = $("#rechazoPanel");
    const $selectDependencia = $("#selectDependencia");
    const $selectUsuario = $("#selectUsuario");
    const $btnConfirmarAprobar = $("#btnConfirmarAprobar");
    const $btnCancelarAprobar = $("#btnCancelarAprobar");
    const $btnConfirmarRechazo = $("#btnConfirmarRechazo");
    const $btnCancelarRechazo = $("#btnCancelarRechazo");
    const $motivoRechazo = $("#motivoRechazo");

    // ========== Mostrar/ocultar paneles ==========

    // Botón Aprobar → muestra panel de turnado
    $btnAprobar.on("click", function () {
        $rechazoPanel.removeClass("mostrar");
        $turnadoPanel.toggleClass("mostrar");
    });

    // Botón Rechazar → muestra panel de rechazo
    $btnRechazar.on("click", function () {
        $turnadoPanel.removeClass("mostrar");
        $rechazoPanel.toggleClass("mostrar");
    });

    // Cancelar aprobación
    $btnCancelarAprobar.on("click", function () {
        $turnadoPanel.removeClass("mostrar");
        $selectDependencia.val("");
        $selectUsuario
            .empty()
            .append(
                '<option value="">— Primero selecciona una dependencia —</option>',
            )
            .prop("disabled", true);
        $btnConfirmarAprobar.prop("disabled", true);
    });

    // Cancelar rechazo
    $btnCancelarRechazo.on("click", function () {
        $rechazoPanel.removeClass("mostrar");
        $motivoRechazo.val("");
    });

    // ========== Cargar usuarios por dependencia ==========

    $selectDependencia.on("change", function () {
        const dependenciaId = $(this).val();

        if (!dependenciaId) {
            $selectUsuario
                .empty()
                .append(
                    '<option value="">— Primero selecciona una dependencia —</option>',
                )
                .prop("disabled", true);
            $btnConfirmarAprobar.prop("disabled", true);
            return;
        }

        // Cargar usuarios vía AJAX
        $.ajax({
            url: "/usuarios-ad/por-dependencia",
            method: "GET",
            data: { dependencia_id: dependenciaId },
            dataType: "json",
            success: function (usuarios) {
                $selectUsuario.empty().prop("disabled", false);
                $selectUsuario.append(
                    '<option value="">— Seleccionar usuario —</option>',
                );

                if (usuarios.length === 0) {
                    $selectUsuario.append(
                        '<option value="" disabled>No hay usuarios en esta dependencia</option>',
                    );
                    $btnConfirmarAprobar.prop("disabled", true);
                    return;
                }

                $.each(usuarios, function (i, usuario) {
                    $selectUsuario.append(
                        '<option value="' +
                            usuario.id_usuario +
                            '">' +
                            usuario.nombre_usuario +
                            "</option>",
                    );
                });

                $btnConfirmarAprobar.prop("disabled", true);
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los usuarios de la dependencia.",
                    confirmButtonColor: "#601028",
                });
            },
        });
    });

    // Habilitar botón confirmar cuando se selecciona un usuario
    $selectUsuario.on("change", function () {
        $btnConfirmarAprobar.prop("disabled", !$(this).val());
    });

    // ========== Confirmar aprobación ==========

    $btnConfirmarAprobar.on("click", function () {
        const solicitudId = $(this).data("solicitud-id");
        const usuarioAdId = $selectUsuario.val();
        const dependenciaText = $selectDependencia
            .find("option:selected")
            .text();
        const usuarioText = $selectUsuario.find("option:selected").text();

        Swal.fire({
            icon: "question",
            title: "¿Aprobar y turnar solicitud?",
            html: `
                <div style="text-align: left; font-size: 0.9rem;">
                    <p><strong>Dependencia:</strong> ${dependenciaText}</p>
                    <p><strong>Usuario responsable:</strong> ${usuarioText}</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: "Sí, aprobar y turnar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#0d7d3d",
            cancelButtonColor: "#64748b",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/solicitudes/" + solicitudId + "/aprobar",
                    method: "POST",
                    data: {
                        fk_usuario_ad: usuarioAdId,
                        _token:
                            window.adminConfig?.csrfToken ??
                            $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "¡Aprobada y turnada!",
                                text: response.message,
                                confirmButtonColor: "#601028",
                            }).then(() => {
                                window.location.href = "/solicitudes";
                            });
                        }
                    },
                    error: function (xhr) {
                        let msg = "Ocurrió un error al aprobar la solicitud.";
                        if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            msg = Object.values(errors).flat().join("<br>");
                        }
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            html: msg,
                            confirmButtonColor: "#601028",
                        });
                    },
                });
            }
        });
    });

    // ========== Confirmar rechazo ==========

    $btnConfirmarRechazo.on("click", function () {
        const motivo = $motivoRechazo.val().trim();
        if (!motivo) {
            Swal.fire({
                icon: "warning",
                title: "Campo requerido",
                text: "Debes escribir el motivo del rechazo.",
                confirmButtonColor: "#601028",
            });
            return;
        }

        const solicitudId = $(this).data("solicitud-id");

        Swal.fire({
            icon: "question",
            title: "¿Rechazar solicitud?",
            text: "Esta acción no se puede deshacer.",
            showCancelButton: true,
            confirmButtonText: "Sí, rechazar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#c62828",
            cancelButtonColor: "#64748b",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/solicitudes/" + solicitudId + "/rechazar",
                    method: "POST",
                    data: {
                        observacion_solicitud: motivo,
                        _token:
                            window.adminConfig?.csrfToken ??
                            $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Rechazada",
                                text: response.message,
                                confirmButtonColor: "#601028",
                            }).then(() => {
                                window.location.href = "/solicitudes";
                            });
                        }
                    },
                    error: function (xhr) {
                        let msg = "Ocurrió un error al rechazar la solicitud.";
                        if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            msg = Object.values(errors).flat().join("<br>");
                        }
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            html: msg,
                            confirmButtonColor: "#601028",
                        });
                    },
                });
            }
        });
    });
});
