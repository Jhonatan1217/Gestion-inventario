document.addEventListener("DOMContentLoaded", () => {

  /* ============================================================
     ICONOS
  ============================================================ */
  if (window.lucide && typeof lucide.createIcons === "function") {
    lucide.createIcons();
  }

  /* ============================================================
     CAMBIO LISTA / GRID
  ============================================================ */
  const btnVistaTabla = document.getElementById("btnVistaTabla");
  const btnVistaTarjetas = document.getElementById("btnVistaTarjetas");
  const viewList = document.getElementById("view-list");
  const viewGrid = document.getElementById("view-grid");

  const setActiveBtn = (active, inactive) => {
    active.classList.add("bg-muted", "text-foreground");
    active.classList.remove("text-muted-foreground");

    inactive.classList.remove("bg-muted", "text-foreground");
    inactive.classList.add("text-muted-foreground");
  };

  const showList = () => {
    viewList?.classList.remove("hidden");
    viewGrid?.classList.add("hidden");
    if (btnVistaTabla && btnVistaTarjetas) {
      setActiveBtn(btnVistaTabla, btnVistaTarjetas);
    }
  };

  const showGrid = () => {
    viewGrid?.classList.remove("hidden");
    viewList?.classList.add("hidden");
    if (btnVistaTabla && btnVistaTarjetas) {
      setActiveBtn(btnVistaTarjetas, btnVistaTabla);
    }
    lucide.createIcons();
  };

  btnVistaTabla?.addEventListener("click", showList);
  btnVistaTarjetas?.addEventListener("click", showGrid);
  showList();

  /* ============================================================
     MODALES – UTILIDADES
  ============================================================ */
  const openModal = (modal) => {
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("overflow-hidden");
    lucide.createIcons();
  };

  const closeModal = (modal) => {
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.classList.remove("overflow-hidden");
  };

  /* ============================================================
     MODAL CREAR BODEGA
  ============================================================ */
  const btnNuevaBodega = document.getElementById("btnNuevaBodega");
  const modalCrear = document.getElementById("modalCrear");
  const formCrearBodega = document.getElementById("formCrearBodega");

  btnNuevaBodega?.addEventListener("click", () => {
    openModal(modalCrear);
  });

  document.getElementById("cerrarModal")?.addEventListener("click", () => {
    closeModal(modalCrear);
  });

  document.getElementById("cancelarModal")?.addEventListener("click", () => {
    closeModal(modalCrear);
  });

  modalCrear?.addEventListener("click", (e) => {
    if (e.target === modalCrear) closeModal(modalCrear);
  });

  /* ============================================================
     CREAR BODEGA – BACKEND
  ============================================================ */
  formCrearBodega?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const codigo = document.getElementById("crearCodigo")?.value.trim();
    const nombre = document.getElementById("crearNombre")?.value.trim();
    const ubicacion = document.getElementById("crearUbicacion")?.value.trim();
    const clasificacion = document.getElementById("crearClasificacion")?.value;

    if (!codigo || !nombre || !ubicacion || !clasificacion) {
      alert("Completa todos los campos obligatorios");
      return;
    }

    try {
      const res = await fetch(
        "/Gestion-inventario/src/controllers/bodega_controller.php?accion=crear",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            codigo_bodega: codigo,
            nombre,
            ubicacion,
            clasificacion_bodega: clasificacion,
            estado: "Activo"
          })
        }
      );

      if (!res.ok) throw new Error();

      closeModal(modalCrear);
      location.reload();

    } catch (err) {
      console.error(err);
      alert("No se pudo crear la bodega");
    }
  });

  /* ============================================================
     MENÚ CONTEXTUAL
  ============================================================ */
  const contextMenu = document.getElementById("context-menu");
  const modalDetalle = document.getElementById("modalDetalle");
  const modalEditar = document.getElementById("modalEditar");

  let selectedData = null;

  function openContextMenu(btn) {
    selectedData = {
      id: btn.dataset.id,
      nombre: btn.dataset.nombre,
      clasificacion: btn.dataset.clasificacion,
      ubicacion: btn.dataset.ubicacion,
      estado: btn.dataset.estado
    };

    const r = btn.getBoundingClientRect();
    const menuWidth = 208;

    contextMenu.style.left = `${r.right + window.scrollX - menuWidth}px`;
    contextMenu.style.top = `${r.bottom + window.scrollY + 8}px`;
    contextMenu.classList.remove("hidden");

    lucide.createIcons();
  }

  function closeContextMenu() {
    contextMenu?.classList.add("hidden");
  }

  document.addEventListener("click", (e) => {
    const btnDots = e.target.closest(".bodegas-btn-dots");
    if (btnDots) {
      e.preventDefault();
      e.stopPropagation();
      openContextMenu(btnDots);
      return;
    }

    if (contextMenu && !contextMenu.contains(e.target)) {
      closeContextMenu();
    }
  });

  /* ============================================================
     ACCIONES CONTEXTUALES
  ============================================================ */
  contextMenu?.querySelector("[data-action='ver']")?.addEventListener("click", () => {
    if (!selectedData) return;

    document.getElementById("detalleId").textContent = selectedData.id;
    document.getElementById("detalleNombre").textContent = selectedData.nombre;
    document.getElementById("detalleClasificacion").textContent = selectedData.clasificacion;
    document.getElementById("detalleUbicacion").textContent = selectedData.ubicacion;

    const estado = document.getElementById("detalleEstado");
    estado.textContent = selectedData.estado;
    estado.className =
      selectedData.estado === "Activo"
        ? "badge-estado-activo"
        : "badge-estado-inactivo";

    openModal(modalDetalle);
    closeContextMenu();
  });

  contextMenu?.querySelector("[data-action='editar']")?.addEventListener("click", () => {
    if (!selectedData) return;

    document.getElementById("editId").value = selectedData.id;
    document.getElementById("editNombre").value = selectedData.nombre;
    document.getElementById("editClasificacion").value = selectedData.clasificacion;
    document.getElementById("editUbicacion").value = selectedData.ubicacion;

    openModal(modalEditar);
    closeContextMenu();
  });

  contextMenu?.querySelector("[data-action='deshabilitar']")?.addEventListener("click", () => {
    if (!selectedData) return;
    alert(`Bodega #${selectedData.id} deshabilitada`);
    closeContextMenu();
  });

  /* ============================================================
     CIERRE DE MODALES
  ============================================================ */
  document.getElementById("cerrarDetalle")?.addEventListener("click", () => closeModal(modalDetalle));
  document.getElementById("cerrarEditar")?.addEventListener("click", () => closeModal(modalEditar));
  document.getElementById("cancelarEditar")?.addEventListener("click", () => closeModal(modalEditar));

  modalDetalle?.addEventListener("click", (e) => {
    if (e.target === modalDetalle) closeModal(modalDetalle);
  });

  modalEditar?.addEventListener("click", (e) => {
    if (e.target === modalEditar) closeModal(modalEditar);
  });

  /* ============================================================
     ESC PARA CERRAR TODO
  ============================================================ */
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal(modalCrear);
      closeModal(modalDetalle);
      closeModal(modalEditar);
      closeContextMenu();
    }
  });

});
