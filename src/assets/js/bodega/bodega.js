document.addEventListener("DOMContentLoaded", () => {
const API_URL = "src/controllers/bodega_controller.php"; 
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

formCrearBodega?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const codigo = document.getElementById("crearCodigo").value.trim();
  const nombre = document.getElementById("crearNombre").value.trim();
  const ubicacion = document.getElementById("crearUbicacion").value.trim();
  const clasificacion = document.getElementById("crearClasificacion").value;

  if (!codigo || !nombre || !ubicacion || !clasificacion) {
    alert("Completa todos los campos obligatorios");
    return;
  }

  try {
    const res = await fetch(
      "/Gestion-inventario/src/controllers/bodega_controller.php?accion=crear",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
        codigo_bodega: codigo,
        nombre: nombre,
        ubicacion: ubicacion,
        clasificacion_bodega: clasificacion
      })

      }
    );

    if (!res.ok) {
      const txt = await res.text();
      console.error(txt);
      throw new Error("Error al crear bodega");
    }

    document.getElementById("modalCrear").classList.add("hidden");
    location.reload();

  } catch (err) {
    console.error(err);
    alert("No se pudo crear la bodega");
  }
});

  const cerrarModal = document.getElementById("cerrarModal");
  const cancelarModal = document.getElementById("cancelarModal");

  btnNuevaBodega?.addEventListener("click", () => {
    openModal(modalCrear);
  });

  cerrarModal?.addEventListener("click", () => {
    closeModal(modalCrear);
  });

  cancelarModal?.addEventListener("click", () => {
    closeModal(modalCrear);
  });

  modalCrear?.addEventListener("click", (e) => {
    if (e.target === modalCrear) {
      closeModal(modalCrear);
    }
  });

 /* ============================================================
   MODAL CREAR SUB-BODEGA
============================================================ */

// BOTÓN Y MODAL
const btnNuevaSubBodega = document.getElementById("btnNuevaSubBodega");
const modalCrearSub = document.getElementById("modalCrearSubBodega");

// FORMULARIO
const formCrearSubBodega = document.getElementById("formCrearSubBodega");

// CAMPOS
const inputBodegaPadre = document.getElementById("subIdBodega");
const inputCodigo = document.getElementById("subCodigo");
const inputClasificacion = document.getElementById("subClasificacion");
const inputNombre = document.getElementById("subNombre");
const inputDescripcion = document.getElementById("subDescripcion");

// BOTONES
const cerrarModalSub = document.getElementById("cerrarModalSub");
const cancelarModalSub = document.getElementById("cancelarModalSub");
const backdropCrearSub = document.getElementById("backdropCrearSub");

/* ============================================================
   ENVIAR FORMULARIO
============================================================ */
formCrearSubBodega?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const idBodega = inputBodegaPadre.value;
  const codigo = inputCodigo.value.trim();
  const clasificacion = inputClasificacion.value;
  const nombre = inputNombre.value.trim();
  const descripcion = inputDescripcion.value.trim();

  if (!idBodega || !codigo || !clasificacion || !nombre) {
    alert("Completa todos los campos obligatorios");
    return;
  }

  try {
    const res = await fetch(
      "/Gestion-inventario/src/controllers/sub_bodega_controller.php?accion=crear",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          codigo_sub_bodega: codigo,
          nombre: nombre,
          descripcion: descripcion,
          clasificacion_bodega: clasificacion,
          codigo_bodega_padre: idBodega
        })
      }
    );

    if (!res.ok) {
      const errorText = await res.text();
      console.error(errorText);
      throw new Error("Error al crear sub-bodega");
    }

    closeModal(modalCrearSub);
    location.reload();

  } catch (err) {
    console.error(err);
    alert("No se pudo crear la sub-bodega");
  }
});

/* ============================================================
   ABRIR / CERRAR MODAL
============================================================ */
btnNuevaSubBodega?.addEventListener("click", (e) => {
  e.stopPropagation();
  openModal(modalCrearSub);
});


cerrarModalSub?.addEventListener("click", () => {
  closeModal(modalCrearSub);
});

cancelarModalSub?.addEventListener("click", () => {
  closeModal(modalCrearSub);
});

backdropCrearSub?.addEventListener("click", (e) => {
  if (e.target === backdropCrearSub) {
    closeModal(modalCrearSub);
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
    codigo: btn.dataset.codigo, 
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

    document.getElementById("detalleId").textContent = selectedData.codigo;
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

    document.getElementById("editIdBodega").value = selectedData.id;
    document.getElementById("editCodigoBodega").value = selectedData.codigo;
    document.getElementById("editNombre").value = selectedData.nombre;
    document.getElementById("editClasificacion").value = selectedData.clasificacion;
    document.getElementById("editUbicacion").value = selectedData.ubicacion;


  openModal(modalEditar);
  closeContextMenu();
});


  contextMenu
    ?.querySelector("[data-action='deshabilitar']")
    ?.addEventListener("click", async () => {

      if (!selectedData) return;

      const confirmar = confirm(
        `¿Seguro que deseas ${selectedData.estado === "Activo" ? "deshabilitar" : "habilitar"} esta bodega?`
      );

      if (!confirmar) return;

      try {
        const res = await fetch(
          "/Gestion-inventario/src/controllers/bodega_controller.php?accion=cambiar_estado",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
            codigo_bodega: selectedData.codigo,
            estado: selectedData.estado === "Activo" ? "Inactivo" : "Activo"
          })
          }
        );

        if (!res.ok) throw new Error();

        closeContextMenu();
        location.reload();

      } catch (error) {
        console.error(error);
        alert("No se pudo cambiar el estado de la bodega");
      }
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

  /* ============================================================
     EDITAR BODEGA – BACKEND
  ============================================================ */
      document.getElementById("guardarEditar").addEventListener("click", () => {

      const id_bodega = document.getElementById("editIdBodega").value;
      const codigo_bodega = document.getElementById("editCodigoBodega").value.trim();
      const nombre = document.getElementById("editNombre").value.trim();
      const ubicacion = document.getElementById("editUbicacion").value.trim();
      const clasificacion_bodega = document.getElementById("editClasificacion").value;

      if (!id_bodega || !codigo_bodega || !nombre || !ubicacion || !clasificacion_bodega) {
        alert("Todos los campos son obligatorios");
        return;
      }

      fetch(`${API_URL}?accion=actualizar`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          id_bodega,
          codigo_bodega,
          nombre,
          ubicacion,
          clasificacion_bodega
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
        } else {
          alert("Bodega actualizada correctamente");
          location.reload();
        }
      })
      .catch(err => {
        console.error(err);
        alert("Error al actualizar la bodega");
      });
    });


});
