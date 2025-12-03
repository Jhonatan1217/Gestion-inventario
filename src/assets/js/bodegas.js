document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons();

  /* ========== SWITCH LISTA / GRID ========== */
  const btnList = document.querySelector(".bodegas-switch-btn[data-view='list']");
  const btnGrid = document.querySelector(".bodegas-switch-btn[data-view='grid']");
  const viewList = document.getElementById("view-list");
  const viewGrid = document.getElementById("view-grid");

  btnList.addEventListener("click", () => {
    btnList.classList.add("active");
    btnGrid.classList.remove("active");
    viewList.classList.remove("hidden");
    viewGrid.classList.add("hidden");
  });

  btnGrid.addEventListener("click", () => {
    btnGrid.classList.add("active");
    btnList.classList.remove("active");
    viewGrid.classList.remove("hidden");
    viewList.classList.add("hidden");
  });

  /* ========== MENÚ CONTEXTUAL ========== */
  const ctxMenu = document.getElementById("context-menu");
  let currentItemData = null;

  // Botón de deshabilitar del menú (lo usamos también más abajo)
  const btnDeshabilitar = document.querySelector(
    ".bodegas-ctx-btn[data-action='deshabilitar']"
  );

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

        // Posicionar menú
        const rect = btn.getBoundingClientRect();
        ctxMenu.style.top = `${rect.bottom + window.scrollY + 6}px`;
        ctxMenu.style.left = `${rect.right - 200}px`;
        ctxMenu.classList.remove("hidden");

        // Actualizar texto del botón (Habilitar / Deshabilitar)
        if (btnDeshabilitar) {
          const label =
            currentItemData.estado === "Activo" ? "Deshabilitar" : "Habilitar";
          btnDeshabilitar.innerHTML = `<i data-lucide="power"></i> ${label}`;
          lucide.createIcons();
        }

        lucide.createIcons();
      });
    });
  }

  attachDotsHandlers();

  document.addEventListener("click", () => {
    ctxMenu.classList.add("hidden");
  });

  /* ========== SWITCH ESTADO SOLO EN GRID ========== */
  document
    .querySelectorAll(".bodegas-card .bodegas-switch input")
    .forEach((input) => {
      input.addEventListener("change", () => {
        const text = input
          .closest(".bodegas-estado")
          .querySelector(".bodegas-estado-text");
        text.textContent = input.checked ? "Activa" : "Inactiva";
      });
    });

  /* ========== MODAL CREAR BODEGA ========== */
  const modalCrear = document.getElementById("modalCrear");
  const btnNueva = document.getElementById("btnNuevaBodega");
  const btnCerrarCrear = document.getElementById("cerrarModal");
  const btnCancelarCrear = document.getElementById("cancelarModal");

  btnNueva.addEventListener("click", () => {
    modalCrear.classList.remove("hidden");
  });

  btnCerrarCrear.addEventListener("click", () => {
    modalCrear.classList.add("hidden");
  });

  btnCancelarCrear.addEventListener("click", () => {
    modalCrear.classList.add("hidden");
  });

  modalCrear.addEventListener("click", (e) => {
    if (e.target === modalCrear) {
      modalCrear.classList.add("hidden");
    }
  });

  /* ========== MODAL DETALLES BODEGA ========== */
  const modalDetalle = document.getElementById("modalDetalle");
  const btnCerrarDetalle = document.getElementById("cerrarDetalle");

  const detalleNombre = document.getElementById("detalleNombre");
  const detalleId = document.getElementById("detalleId");
  const detalleClasificacion = document.getElementById("detalleClasificacion");
  const detalleTipo = document.getElementById("detalleTipo");
  const detalleUbicacion = document.getElementById("detalleUbicacion");
  const detalleEstado = document.getElementById("detalleEstado");

  const btnVerDetalles = document.querySelector(
    ".bodegas-ctx-btn[data-action='ver']"
  );
  const btnEditar = document.querySelector(
    ".bodegas-ctx-btn[data-action='editar']"
  );

  btnVerDetalles.addEventListener("click", () => {
    if (!currentItemData) return;

    detalleNombre.textContent = currentItemData.nombre;
    detalleId.textContent = currentItemData.id;
    detalleClasificacion.textContent = currentItemData.clasificacion;
    detalleTipo.textContent = currentItemData.tipo;
    detalleUbicacion.textContent = currentItemData.ubicacion;
    detalleEstado.textContent = currentItemData.estado;

    detalleEstado.classList.remove(
      "bodegas-tag-status-active",
      "bodegas-tag-status-inactive"
    );
    if (currentItemData.estado === "Activo") {
      detalleEstado.classList.add("bodegas-tag-status-active");
    } else {
      detalleEstado.classList.add("bodegas-tag-status-inactivo");
    }

    modalDetalle.classList.remove("hidden");
    ctxMenu.classList.add("hidden");
  });

  btnCerrarDetalle.addEventListener("click", () => {
    modalDetalle.classList.add("hidden");
  });

  modalDetalle.addEventListener("click", (e) => {
    if (e.target === modalDetalle) {
      modalDetalle.classList.add("hidden");
    }
  });

  /* ========== MODAL EDITAR BODEGA ========== */
  const modalEditar = document.getElementById("modalEditar");
  const btnCerrarEditar = document.getElementById("cerrarEditar");
  const btnCancelarEditar = document.getElementById("cancelarEditar");
  const btnGuardarEditar = document.getElementById("guardarEditar");

  const editId = document.getElementById("editId");
  const editTipo = document.getElementById("editTipo");
  const editClasificacion = document.getElementById("editClasificacion");
  const editNombre = document.getElementById("editNombre");
  const editUbicacion = document.getElementById("editUbicacion");

  // Abrir modal Editar
  btnEditar.addEventListener("click", () => {
    if (!currentItemData) return;

    editId.value = currentItemData.id || "";
    editNombre.value = currentItemData.nombre || "";
    editUbicacion.value = currentItemData.ubicacion || "";
    if (editTipo) editTipo.value = currentItemData.tipo || "Bodega";
    if (editClasificacion)
      editClasificacion.value = currentItemData.clasificacion || "Eléctrico";

    modalEditar.classList.remove("hidden");
    ctxMenu.classList.add("hidden");
  });

  btnCerrarEditar.addEventListener("click", () => {
    modalEditar.classList.add("hidden");
  });

  btnCancelarEditar.addEventListener("click", () => {
    modalEditar.classList.add("hidden");
  });

  modalEditar.addEventListener("click", (e) => {
    if (e.target === modalEditar) {
      modalEditar.classList.add("hidden");
    }
  });

  btnGuardarEditar.addEventListener("click", (e) => {
    e.preventDefault();
    // Aquí luego conectamos con backend para guardar cambios
    modalEditar.classList.add("hidden");
  });

  /* ========== DESHABILITAR / HABILITAR ========== */
  btnDeshabilitar.addEventListener("click", () => {
    if (!currentItemData) return;

    const id = currentItemData.id;

    // Buscar el botón de puntos de esa bodega
    const btnDots = document.querySelector(
      `.bodegas-btn-dots[data-id='${id}']`
    );

    // Obtener el estado actual
    const estadoActual = btnDots.dataset.estado;
    const nuevoEstado = estadoActual === "Activo" ? "Inactivo" : "Activo";

    // Actualizar también el objeto en memoria
    currentItemData.estado = nuevoEstado;

    // Guardarlo en el dataset
    btnDots.dataset.estado = nuevoEstado;

    /* ---- ACTUALIZAR EN VISTA LISTA ---- */
    const fila = btnDots.closest("tr");
    if (fila) {
      const estadoTd = fila.querySelector(".bodegas-tag-status");
      estadoTd.textContent = nuevoEstado;

      estadoTd.classList.remove(
        "bodegas-tag-status-active",
        "bodegas-tag-status-inactive"
      );

      if (nuevoEstado === "Activo") {
        estadoTd.classList.add("bodegas-tag-status-active");
      } else {
        estadoTd.classList.add("bodegas-tag-status-inactive");
      }
    }

    /* ---- ACTUALIZAR EN VISTA GRID ---- */
    const card = btnDots.closest(".bodegas-card");
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

    // Ajustar texto del botón para la próxima vez que se abra el menú
    if (btnDeshabilitar) {
      const label =
        nuevoEstado === "Activo" ? "Deshabilitar" : "Habilitar";
      btnDeshabilitar.innerHTML = `<i data-lucide="power"></i> ${label}`;
      lucide.createIcons();
    }

    // Cerrar menú contextual
    ctxMenu.classList.add("hidden");
  });

});
