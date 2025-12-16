document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();

    /* ============================================================
       ========== SWITCH LISTA / GRID ==========
    ============================================================ */
    const btnsView = document.querySelectorAll(".bodegas-switch-btn");
    const viewList = document.getElementById("view-list");
    const viewGrid = document.getElementById("view-grid");

    btnsView.forEach(btn => {
        btn.addEventListener("click", () => {
            btnsView.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const view = btn.getAttribute("data-view");

            if (view === "list") {
                viewList.classList.remove("hidden");
                viewGrid.classList.add("hidden");
            } else {
                viewGrid.classList.remove("hidden");
                viewList.classList.add("hidden");
            }
        });
    });

    /* ============================================================
       ========== MENÚ CONTEXTUAL (3 PUNTOS) ==========
    ============================================================ */
    const contextMenu = document.getElementById("context-menu");
    let selectedData = null;

    function openContextMenu(e, data) {
        selectedData = data;
        contextMenu.style.left = `${e.pageX}px`;
        contextMenu.style.top = `${e.pageY}px`;
        contextMenu.classList.remove("hidden");
    }

    function closeContextMenu() {
        contextMenu.classList.add("hidden");
    }

    document.querySelectorAll(".bodegas-btn-dots").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            const data = {
                id: btn.dataset.id,
                nombre: btn.dataset.nombre,
                clasificacion: btn.dataset.clasificacion,
                ubicacion: btn.dataset.ubicacion,
                tipo: btn.dataset.tipo,
                estado: btn.dataset.estado
            };
            openContextMenu(e, data);
        });
    });

    document.addEventListener("click", e => {
        if (!contextMenu.contains(e.target) && !e.target.closest(".bodegas-btn-dots")) {
            closeContextMenu();
        }
    });

    /* ============================================================
       ========== ACCIONES DEL MENÚ ==========
    ============================================================ */
    const modalDetalle = document.getElementById("modalDetalle");
    const modalEditar = document.getElementById("modalEditar");

    contextMenu.querySelector("[data-action='ver']").addEventListener("click", () => {
        if (!selectedData) return;

        document.getElementById("detalleId").textContent = selectedData.id;
        document.getElementById("detalleNombre").textContent = selectedData.nombre;
        document.getElementById("detalleClasificacion").textContent = selectedData.clasificacion;
        document.getElementById("detalleTipo").textContent = selectedData.tipo;
        document.getElementById("detalleUbicacion").textContent = selectedData.ubicacion;

        const est = document.getElementById("detalleEstado");
        est.textContent = selectedData.estado;
        est.className = "bodegas-tag-status " + 
            (selectedData.estado === "Activo" ? "bodegas-tag-status-active" : "bodegas-tag-status-inactive");

        modalDetalle.classList.remove("hidden");
        closeContextMenu();
    });

    contextMenu.querySelector("[data-action='editar']").addEventListener("click", () => {
        if (!selectedData) return;

        document.getElementById("editId").value = selectedData.id;
        document.getElementById("editNombre").value = selectedData.nombre;
        document.getElementById("editClasificacion").value = selectedData.clasificacion;
        document.getElementById("editUbicacion").value = selectedData.ubicacion;
        document.getElementById("editTipo").value = selectedData.tipo;

        modalEditar.classList.remove("hidden");
        closeContextMenu();
    });

    contextMenu.querySelector("[data-action='deshabilitar']").addEventListener("click", () => {
        if (!selectedData) return;

        alert(`Bodega #${selectedData.id} deshabilitada`);
        closeContextMenu();
    });

    /* ============================================================
       ========== CERRAR MODALES ==========
    ============================================================ */
    document.getElementById("cerrarDetalle").onclick = () => modalDetalle.classList.add("hidden");
    document.getElementById("cerrarEditar").onclick = () => modalEditar.classList.add("hidden");
    document.getElementById("cancelarEditar").onclick = () => modalEditar.classList.add("hidden");

    /* ============================================================
       ========== SWITCHES ACTIVA / INACTIVA (LISTA + GRID) ==========
    ============================================================ */

    function initSwitches() {
        // SWITCH EN LISTA
        const listRows = document.querySelectorAll("#view-list .bodegas-row");

        listRows.forEach(row => {
            const estadoTag = row.querySelector(".bodegas-tag-status");
            const estadoActual = estadoTag.textContent.trim();

            // No hay switch visual en lista, pero podrías agregarlo si quieres
        });

        // SWITCH EN GRID (EL IMPORTANTE)
        const gridSwitches = document.querySelectorAll("#view-grid .bodegas-switch input");

        gridSwitches.forEach(sw => {
            sw.addEventListener("change", () => {
                const card = sw.closest(".bodegas-card");
                const text = card.querySelector(".bodegas-estado-text");

                text.textContent = sw.checked ? "Activa" : "Inactiva";
            });
        });
    }

    initSwitches();
});
