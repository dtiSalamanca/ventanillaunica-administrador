/*!
 * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
 */
//
// Scripts
//

window.addEventListener("DOMContentLoaded", (event) => {
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector("#sidebarToggle");
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener("click", (event) => {
            event.preventDefault();
            document.body.classList.toggle("sb-sidenav-toggled");
            localStorage.setItem(
                "sb|sidebar-toggle",
                document.body.classList.contains("sb-sidenav-toggled")
            );
        });
    }

    // Función mejorada para inicializar dropdowns (excluyendo el dropdown del usuario)
    function initializeDropdowns() {
        console.log("Inicializando dropdowns generales...");

        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === "undefined") {
            console.error("Bootstrap no está cargado correctamente");
            setupManualDropdown();
            return;
        }

        // Inicializar dropdowns de Bootstrap de manera más robusta
        // Excluir el dropdown del usuario que se maneja por separado
        const dropdownElementList = document.querySelectorAll(
            ".dropdown-toggle:not(#navbarDropdown)"
        );
        console.log(
            "Dropdowns generales encontrados:",
            dropdownElementList.length
        );

        dropdownElementList.forEach(function (dropdownToggleEl) {
            // Verificar si ya está inicializado
            if (
                dropdownToggleEl.getAttribute("data-bs-initialized") === "true"
            ) {
                console.log(
                    "Dropdown general ya inicializado:",
                    dropdownToggleEl.id
                );
                return;
            }

            try {
                // Destruir instancia previa si existe
                const existingDropdown =
                    bootstrap.Dropdown.getInstance(dropdownToggleEl);
                if (existingDropdown) {
                    existingDropdown.dispose();
                }

                // Crear nueva instancia
                new bootstrap.Dropdown(dropdownToggleEl, {
                    autoClose: true,
                    boundary: "viewport",
                });

                dropdownToggleEl.setAttribute("data-bs-initialized", "true");
                console.log(
                    "Dropdown general inicializado correctamente:",
                    dropdownToggleEl.id
                );
            } catch (error) {
                console.error(
                    "Error inicializando dropdown general:",
                    dropdownToggleEl.id,
                    error
                );
                setupManualDropdown();
            }
        });
    }

    // Función de fallback para dropdown manual (excluyendo el dropdown del usuario)
    function setupManualDropdown() {
        console.log("Configurando dropdown manual general...");

        const dropdownToggles = document.querySelectorAll(
            ".dropdown-toggle:not(#navbarDropdown)"
        );

        dropdownToggles.forEach(function (dropdownToggle) {
            const dropdownMenu = dropdownToggle.nextElementSibling;

            if (
                dropdownToggle &&
                dropdownMenu &&
                dropdownMenu.classList.contains("dropdown-menu")
            ) {
                // Remover listeners previos
                dropdownToggle.removeEventListener(
                    "click",
                    dropdownToggle._manualClickHandler
                );

                // Crear nuevo handler
                dropdownToggle._manualClickHandler = function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log(
                        "Click en dropdown manual general detectado:",
                        dropdownToggle.id
                    );

                    // Cerrar otros dropdowns
                    document
                        .querySelectorAll(".dropdown-menu.show")
                        .forEach(function (menu) {
                            if (menu !== dropdownMenu) {
                                menu.classList.remove("show");
                            }
                        });

                    // Toggle dropdown actual
                    dropdownMenu.classList.toggle("show");
                };

                dropdownToggle.addEventListener(
                    "click",
                    dropdownToggle._manualClickHandler
                );
            }
        });

        // Cerrar dropdowns al hacer click fuera
        document.removeEventListener("click", document._dropdownOutsideHandler);
        document._dropdownOutsideHandler = function (e) {
            if (!e.target.closest(".dropdown")) {
                document
                    .querySelectorAll(".dropdown-menu.show")
                    .forEach(function (menu) {
                        menu.classList.remove("show");
                    });
            }
        };
        document.addEventListener("click", document._dropdownOutsideHandler);
    }

    // Inicializar dropdowns después de un pequeño delay para asegurar que Bootstrap esté listo
    setTimeout(function () {
        initializeDropdowns();
    }, 100);

    // Re-inicializar dropdowns si Bootstrap se carga después
    if (typeof bootstrap === "undefined") {
        // Esperar a que Bootstrap se cargue
        const checkBootstrap = setInterval(function () {
            if (typeof bootstrap !== "undefined") {
                clearInterval(checkBootstrap);
                initializeDropdowns();
            }
        }, 100);
    }
});
