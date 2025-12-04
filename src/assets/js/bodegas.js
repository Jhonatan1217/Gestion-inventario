document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();

    /* ======================================================
       ========== SWITCH LISTA / GRID ========== 
    ====================================================== */
    const btnList = document.querySelector(".bodegas-switch-btn[data-view='list']");
    const btnGrid = document.querySelector(".bodegas-switch-btn[data-view='grid']");
    const viewList = document.getElementById("view-list");
    const viewGrid = document.getElementById("view-grid");

    btnList.addEventListener("click", () => {
        btnList.classList.add("active");
        btnGrid.classList.remove("active");
        viewList.classList.remove("hidden");
        viewGrid.classList.add("hidden");
        lucide.createIcons();
    });

    btnGrid.addEventListener("click", () => {
        btnGrid.classList.add("active");
        btnList.classList.remove("active");
        viewGrid.classList.remove("hidden");
        viewList.classList.add("hidden");
        lucide.createIcons();
    });

    /* ======================================================
       ========== MENÚ CONTEXTUAL ========== 
    ====================================================== */
    const ctxMenu = document.getElementById("context-menu");
    let currentItemData = null;

    function attachDotsHandlers() {
        document.querySelectorAll(".bodegas-btn-dots").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();

                currentItemData = {
                    id: btn.dataset.id,
                    nombre: btn.dataset.nombre,
                    clasificacion: btn.dataset.clasificacion,
                    ubicacion: btn.dataset.ubicacion,
                    tipo: btn.dataset.tipo,
                    estado: btn.dataset.estado,
                };

                const rect = btn.getBoundingClientRect();
                ctxMenu.style.top = `${rect.bottom + window.scrollY + 8}px`;
                ctxMenu.style.left = `${rect.right - 200}px`;
                ctxMenu.classList.remove("hidden");

                // Cambiar texto dinámico del botón deshabilitar/habilitar
                actualizarBotonHabilitar(currentItemData.estado);

                lucide.createIcons();
            });
        });
    }

    attachDotsHandlers();

    document.addEventListener("click", () => {
        ctxMenu.classList.add("hidden");
    });

    /* ======================================================
       ========== FUNCIÓN PARA CAMBIAR TEXTO DES/HAB ========== 
    ====================================================== */
    function actualizarBotonHabilitar(estado) {
        const btnDes = document.querySelector(".bodegas-ctx-btn[data-action='deshabilitar']");
        if (estado === "Activo") {
            btnDes.innerHTML = `<i data-lucide="power"></i> Deshabilitar`;
        } else {
            btnDes.innerHTML = `<i data-lucide="power"></i> Habilitar`;
        }
        lucide.createIcons();
    }

    /* ======================================================
       ========== MODAL CREAR BODEGA ========== 
    ====================================================== */
    const modalCrear = document.getElementById("modalCrear");
    const btnNueva = document.getElementById("btnNuevaBodega");
    const btnCerrarCrear = document.getElementById("cerrarModal");
    const btnCancelarCrear = document.getElementById("cancelarModal");

    btnNueva.addEventListener("click", () => modalCrear.classList.remove("hidden"));
    btnCerrarCrear.addEventListener("click", () => modalCrear.classList.add("hidden"));
    btnCancelarCrear.addEventListener("click", () => modalCrear.classList.add("hidden"));

    modalCrear.addEventListener("click", (e) => {
        if (e.target === modalCrear) modalCrear.classList.add("hidden");
    });

    /* ======================================================
       ========== MODAL DETALLES BODEGA ========== 
    ====================================================== */
    const modalDetalle = document.getElementById("modalDetalle");
    const btnCerrarDetalle = document.getElementById("cerrarDetalle");

    const detalleNombre = document.getElementById("detalleNombre");
    const detalleId = document.getElementById("detalleId");
    const detalleClasificacion = document.getElementById("detalleClasificacion");
    const detalleTipo = document.getElementById("detalleTipo");
    const detalleUbicacion = document.getElementById("detalleUbicacion");
    const detalleEstado = document.getElementById("detalleEstado");

    const btnVerDetalles = document.querySelector(".bodegas-ctx-btn[data-action='ver']");
    const btnEditar = document.querySelector(".bodegas-ctx-btn[data-action='editar']");

    btnVerDetalles.addEventListener("click", () => {
        if (!currentItemData) return;

        detalleNombre.textContent = currentItemData.nombre;
        detalleId.textContent = currentItemData.id;
        detalleClasificacion.textContent = currentItemData.clasificacion;
        detalleTipo.textContent = currentItemData.tipo;
        detalleUbicacion.textContent = currentItemData.ubicacion;
        detalleEstado.textContent = currentItemData.estado;

        detalleEstado.classList.remove("bodegas-tag-status-active", "bodegas-tag-status-inactive");
        detalleEstado.classList.add(
            currentItemData.estado === "Activo"
                ? "bodegas-tag-status-active"
                : "bodegas-tag-status-inactive"
        );

        modalDetalle.classList.remove("hidden");
        ctxMenu.classList.add("hidden");
    });

    btnCerrarDetalle.addEventListener("click", () => modalDetalle.classList.add("hidden"));
    modalDetalle.addEventListener("click", (e) => {
        if (e.target === modalDetalle) modalDetalle.classList.add("hidden");
    });

    /* ======================================================
       ========== MODAL EDITAR BODEGA ========== 
    ====================================================== */
    const modalEditar = document.getElementById("modalEditar");
    const btnCerrarEditar = document.getElementById("cerrarEditar");
    const btnCancelarEditar = document.getElementById("cancelarEditar");
    const btnGuardarEditar = document.getElementById("guardarEditar");

    const editId = document.getElementById("editId");
    const editTipo = document.getElementById("editTipo");
    const editClasificacion = document.getElementById("editClasificacion");
    const editNombre = document.getElementById("editNombre");
    const editUbicacion = document.getElementById("editUbicacion");

    btnEditar.addEventListener("click", () => {
        if (!currentItemData) return;

        editId.value = currentItemData.id;
        editTipo.value = currentItemData.tipo;
        editClasificacion.value = currentItemData.clasificacion;
        editNombre.value = currentItemData.nombre;
        editUbicacion.value = currentItemData.ubicacion;

        modalEditar.classList.remove("hidden");
        ctxMenu.classList.add("hidden");
    });

    btnCerrarEditar.addEventListener("click", () => modalEditar.classList.add("hidden"));
    btnCancelarEditar.addEventListener("click", () => modalEditar.classList.add("hidden"));

    modalEditar.addEventListener("click", (e) => {
        if (e.target === modalEditar) modalEditar.classList.add("hidden");
    });

    btnGuardarEditar.addEventListener("click", () => {
        modalEditar.classList.add("hidden");
    });

    /* ======================================================
       ========== ACTUALIZAR LISTA + GRID (CORREGIDO) ========== 
    ====================================================== */
    function actualizarEstado(id, nuevoEstado) {
        document.querySelectorAll(`.bodegas-btn-dots[data-id='${id}']`).forEach((btn) => {
            btn.dataset.estado = nuevoEstado;

            // === LISTA ===
            const fila = btn.closest("tr");
            if (fila) {
                const estadoTd = fila.querySelector(".bodegas-tag-status");
                estadoTd.textContent = nuevoEstado;

                estadoTd.classList.remove("bodegas-tag-status-active", "bodegas-tag-status-inactive");

                estadoTd.classList.add(
                    nuevoEstado === "Activo" ? "bodegas-tag-status-active" : "bodegas-tag-status-inactive"
                );
            }

            // === GRID ===
            const card = btn.closest(".bodegas-card");
            if (card) {
                const textEstado = card.querySelector(".bodegas-estado-text");
                const switchInput = card.querySelector(".bodegas-switch input");

                if (textEstado) {
                    textEstado.textContent = nuevoEstado === "Activo" ? "Activa" : "Inactiva";
                }
                if (switchInput) {
                    switchInput.checked = nuevoEstado === "Activo";
                }
            }
        });
    }

    /* ======================================================
       ========== DESHABILITAR / HABILITAR ========== 
    ====================================================== */
    const btnDeshabilitar = document.querySelector(".bodegas-ctx-btn[data-action='deshabilitar']");

    btnDeshabilitar.addEventListener("click", () => {
        if (!currentItemData) return;

        const id = currentItemData.id;
        const estadoActual = currentItemData.estado;
        const nuevoEstado = estadoActual === "Activo" ? "Inactivo" : "Activo";

        currentItemData.estado = nuevoEstado;

        actualizarEstado(id, nuevoEstado);
        actualizarBotonHabilitar(nuevoEstado);

        ctxMenu.classList.add("hidden");
    });
});
