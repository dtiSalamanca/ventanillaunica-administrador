/**
 * Trámites Turnados Detalles - Aprobar/Rechazar solicitudes con subida de archivo
 * Depende de: jQuery, SweetAlert2, window.enlaceDetallesRoutes
 */
(function () {
    const routes = window.enlaceDetallesRoutes || {};

    if (!routes.aprobar || !routes.rechazar) {
        console.warn(
            "[TramitesTurnadosDetalles] No se encontraron las rutas del endpoint.",
        );
        return;
    }

    /* ───────── Elementos del DOM ───────── */
    const $btnAprobar = document.getElementById("btnAprobar");
    const $btnRechazar = document.getElementById("btnRechazar");
    const $aprobacionPanel = document.getElementById("aprobacionPanel");
    const $rechazoPanel = document.getElementById("rechazoPanel");
    const $btnConfirmarAprobar = document.getElementById("btnConfirmarAprobar");
    const $btnCancelarAprobar = document.getElementById("btnCancelarAprobar");
    const $btnConfirmarRechazo = document.getElementById("btnConfirmarRechazo");
    const $btnCancelarRechazo = document.getElementById("btnCancelarRechazo");
    const $motivoRechazo = document.getElementById("motivoRechazo");
    const $resolucionNota = document.getElementById("resolucionNota");
    const $uploadZone = document.getElementById("uploadZone");
    const $fileInput = document.getElementById("fileInput");
    const $uploadText = document.getElementById("uploadText");
    const $uploadIcon = document.getElementById("uploadIcon");
    const $uploadFileInfo = document.getElementById("uploadFileInfo");
    const $fileNameText = document.getElementById("fileNameText");
    const $btnChangeFile = document.getElementById("btnChangeFile");
    const $btnRemoveFile = document.getElementById("btnRemoveFile");
    const $fileError = document.getElementById("fileError");

    let selectedFile = null;

    /* ───────── Mostrar panel de aprobacion ───────── */
    if ($btnAprobar) {
        $btnAprobar.addEventListener("click", function () {
            $aprobacionPanel.classList.add("mostrar");
            $btnAprobar.disabled = true;
            if ($btnRechazar) $btnRechazar.disabled = true;
        });
    }

    /* ───────── Cancelar aprobacion ───────── */
    if ($btnCancelarAprobar) {
        $btnCancelarAprobar.addEventListener("click", function () {
            resetAprobacionPanel();
        });
    }

    function resetAprobacionPanel() {
        $aprobacionPanel.classList.remove("mostrar");
        selectedFile = null;
        $fileInput.value = "";
        resetUploadZone();
        if ($btnAprobar) $btnAprobar.disabled = false;
        if ($btnRechazar) $btnRechazar.disabled = false;
    }

    /* ───────── Logica de upload ───────── */
    function resetUploadZone() {
        $uploadZone.classList.remove("has-file");
        $uploadIcon.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i>';
        $uploadText.innerHTML =
            '<span class="upload-zone-browse">Browse File to upload!</span>';
        $uploadFileInfo.style.display = "none";
        $fileError.style.display = "none";
    }

    function showFileSelected(file) {
        $uploadZone.classList.add("has-file");
        $uploadIcon.innerHTML = '<i class="fa-regular fa-file-pdf"></i>';
        $uploadText.textContent = "Archivo seleccionado:";
        $fileNameText.textContent = file.name;
        $uploadFileInfo.style.display = "flex";
        $fileError.style.display = "none";
    }

    // Click en la zona abre el selector
    if ($uploadZone) {
        $uploadZone.addEventListener("click", function (e) {
            if (e.target.closest(".upload-file-actions")) return;
            $fileInput.click();
        });
    }

    // Cambiar archivo
    if ($btnChangeFile) {
        $btnChangeFile.addEventListener("click", function (e) {
            e.stopPropagation();
            $fileInput.click();
        });
    }

    // Quitar archivo
    if ($btnRemoveFile) {
        $btnRemoveFile.addEventListener("click", function (e) {
            e.stopPropagation();
            selectedFile = null;
            $fileInput.value = "";
            resetUploadZone();
        });
    }

    // Validar y mostrar archivo seleccionado
    if ($fileInput) {
        $fileInput.addEventListener("change", function () {
            const file = this.files[0];
            if (!file) return;

            const validTypes = [
                "application/pdf",
                "image/jpeg",
                "image/jpg",
                "image/png",
            ];
            const maxSize = 10 * 1024 * 1024; // 10 MB

            if (!validTypes.includes(file.type)) {
                $fileError.textContent =
                    "Tipo de archivo no permitido. Solo PDF, JPG o PNG.";
                $fileError.style.display = "block";
                this.value = "";
                return;
            }

            if (file.size > maxSize) {
                $fileError.textContent =
                    "El archivo excede el tamano maximo de 10 MB.";
                $fileError.style.display = "block";
                this.value = "";
                return;
            }

            selectedFile = file;
            showFileSelected(file);
        });
    }

    /* ─────── Arrastrar y soltar ─────── */
    if ($uploadZone) {
        $uploadZone.addEventListener("dragover", function (e) {
            e.preventDefault();
            this.classList.add("dragover");
        });

        $uploadZone.addEventListener("dragleave", function () {
            this.classList.remove("dragover");
        });

        $uploadZone.addEventListener("drop", function (e) {
            e.preventDefault();
            this.classList.remove("dragover");
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                $fileInput.files = files;
                $fileInput.dispatchEvent(new Event("change"));
            }
        });
    }

    /* ───────── Confirmar aprobacion con archivo ───────── */
    if ($btnConfirmarAprobar) {
        $btnConfirmarAprobar.addEventListener("click", function () {
            const nota = $resolucionNota ? $resolucionNota.value.trim() : "";

            Swal.fire({
                title: "Aprobar y pagar?",
                text: selectedFile
                    ? "Se marcara como atendida y se adjuntara el documento de resolucion."
                    : "Se marcara como atendida. No se adjunto documento de resolucion.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#1e5c50",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Si, aprobar",
                cancelButtonText: "Cancelar",
            }).then(function (result) {
                if (!result.isConfirmed) return;

                const formData = new FormData();
                formData.append("resolucion_solicitud", nota || "Atendido");
                if (selectedFile) {
                    formData.append("documento_resolucion", selectedFile);
                }

                fetch(routes.aprobar, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": window.adminConfig?.csrfToken || "",
                        Accept: "application/json",
                    },
                    body: formData,
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Aprobado!",
                                text:
                                    data.message ||
                                    "Solicitud atendida correctamente.",
                                confirmButtonColor: "#1e5c50",
                            }).then(function () {
                                window.location.href = routes.listado;
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text:
                                    data.message ||
                                    "No se pudo aprobar la solicitud.",
                                confirmButtonColor: "#601028",
                            });
                        }
                    })
                    .catch(function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error de conexion",
                            text: "Ocurrio un error al comunicarse con el servidor.",
                            confirmButtonColor: "#601028",
                        });
                    });
            });
        });
    }

    /* ───────── Mostrar/ocultar panel de rechazo ───────── */
    if ($btnRechazar) {
        $btnRechazar.addEventListener("click", function () {
            $rechazoPanel.style.display = "block";
            $btnRechazar.disabled = true;
            if ($btnAprobar) $btnAprobar.disabled = true;
        });
    }

    if ($btnCancelarRechazo) {
        $btnCancelarRechazo.addEventListener("click", function () {
            $rechazoPanel.style.display = "none";
            $motivoRechazo.value = "";
            $btnRechazar.disabled = false;
            if ($btnAprobar) $btnAprobar.disabled = false;
        });
    }

    /* ───────── Confirmar rechazo ───────── */
    if ($btnConfirmarRechazo) {
        $btnConfirmarRechazo.addEventListener("click", function () {
            var observacion = $motivoRechazo.value.trim();

            if (!observacion) {
                Swal.fire({
                    icon: "warning",
                    title: "Campo requerido",
                    text: "Debes escribir un motivo para rechazar la solicitud.",
                    confirmButtonColor: "#601028",
                });
                return;
            }

            Swal.fire({
                title: "Rechazar tramite?",
                text: "Esta accion no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Si, rechazar",
                cancelButtonText: "Cancelar",
            }).then(function (result) {
                if (!result.isConfirmed) return;

                fetch(routes.rechazar, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": window.adminConfig?.csrfToken || "",
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        observacion_solicitud: observacion,
                    }),
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Rechazado",
                                text:
                                    data.message ||
                                    "Solicitud rechazada correctamente.",
                                confirmButtonColor: "#1e5c50",
                            }).then(function () {
                                window.location.href = routes.listado;
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text:
                                    data.message ||
                                    "No se pudo rechazar la solicitud.",
                                confirmButtonColor: "#601028",
                            });
                        }
                    })
                    .catch(function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error de conexion",
                            text: "Ocurrio un error al comunicarse con el servidor.",
                            confirmButtonColor: "#601028",
                        });
                    });
            });
        });
    }
})();
