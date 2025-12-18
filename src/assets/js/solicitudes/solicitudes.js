// ============================================================
//  MÓDULO SOLICITUDES – JS CORREGIDO CON NOMBRES REALES DE BD
// ============================================================

const API = "src/controllers/solicitudes_controller.php";

// ============================================================
//  CONFIGURACIÓN CON NOMBRES REALES DE LA BD
// ============================================================
const CONFIG = {
    // Mapeo de nombres de columnas EXACTAMENTE como están en la BD
    COLUMNAS: {
        ID: "id_solicitud",
        FECHA: "fecha_solicitud",
        ESTADO: "estado",
        FICHA: "id_ficha",
        PROGRAMA: "id_programa",
        RAE: "id_rae",
        ACTIVIDAD: "id_actividad",
        SOLICITANTE: "id_usuario_solicitante",
        APROBADOR: "id_usuario_aprobador",
        OBSERVACIONES: "observaciones",
        FECHA_RESPUESTA: "fecha_respuesta"
    },
    
    // Estados en la BD vs estados para mostrar
    ESTADOS_BD: {
        'Pendiente': 'pendiente',
        'Aprobada': 'aprobada',
        'Rechazada': 'rechazada',
        'Entregada': 'entregada'
    },
    
    LABELS: {
        pendiente: "Pendiente",
        aprobada: "Aprobada",
        rechazada: "Rechazada",
        entregada: "Entregada"
    },
    
    ICONS: {
        pendiente: "clock",
        aprobada: "check-circle",
        entregada: "package-check",
        rechazada: "x-circle"
    },
    
    PAGE_SIZE: 9
};

// ============================================================
//  ESTADO GLOBAL
// ============================================================
let estadoApp = {
    solicitudes: [],
    filtroActivo: "todas",
    paginaActual: 1,
    materialesSeleccionados: [],
    datosFormulario: {
        programa: "",
        rae: "",
        ficha: "",
        actividad: "",
        observaciones: ""
    }
};

// ============================================================
//  SELECTORES
// ============================================================
const selectores = {
    // Botones principales
    btnNueva: document.getElementById("sol-btn-nueva"),
    modal: document.getElementById("sol-modal"),
    btnCerrarModal: document.getElementById("sol-modal-cerrar"),
    btnCancelar: document.getElementById("sol-btn-cancelar"),
    
    // Pasos del modal
    paso1: document.getElementById("sol-paso-1"),
    paso2: document.getElementById("sol-paso-2"),
    btnPaso2: document.getElementById("sol-btn-ir-paso-2"),
    btnVolver: document.getElementById("sol-btn-volver"),
    btnGuardar: document.getElementById("sol-btn-guardar"),
    
    // Contenedores
    contenedorCards: document.getElementById("sol-cards"),
    paginacion: document.getElementById("sol-pagination"),
    filtros: document.querySelectorAll(".sol-filtro-btn"),
    
    // Formulario
    formNueva: document.getElementById("sol-form-nueva"),
    selectPrograma: document.getElementById("programa"),
    selectRae: document.getElementById("rae"),
    selectFicha: document.getElementById("ficha"),
    textareaObservaciones: document.getElementById("observaciones"),
    
    // Materiales
    selectMaterial: document.getElementById("material-select"),
    inputCantidad: document.getElementById("material-cantidad"),
    btnAgregarMaterial: document.getElementById("btn-agregar-material"),
    listaMateriales: document.getElementById("lista-materiales"),
    
    // Resumen
    resumenPendientes: document.getElementById("resumen-pendientes"),
    resumenAprobadas: document.getElementById("resumen-aprobadas"),
    resumenEntregadas: document.getElementById("resumen-entregadas"),
    resumenRechazadas: document.getElementById("resumen-rechazadas")
};

// ============================================================
//  FUNCIONES DE UTILIDAD
// ============================================================
const utilidades = {
    normalizarEstado(estadoBD) {
        // Convierte el estado de BD a nuestro formato interno
        const estado = String(estadoBD).toLowerCase().trim();
        return CONFIG.ESTADOS_BD[estadoBD] || "pendiente";
    },

    extraerDatosSolicitud(solicitudBD) {
        console.log("🔍 Procesando solicitud de BD:", solicitudBD);
        
        return {
            id: solicitudBD[CONFIG.COLUMNAS.ID] || solicitudBD.id_solicitud || solicitudBD.id || "N/A",
            fecha: this.formatearFecha(solicitudBD[CONFIG.COLUMNAS.FECHA] || solicitudBD.fecha_solicitud),
            ficha: solicitudBD[CONFIG.COLUMNAS.FICHA] || solicitudBD.id_ficha || solicitudBD.numero_ficha || "N/A",
            estado: this.normalizarEstado(solicitudBD[CONFIG.COLUMNAS.ESTADO] || solicitudBD.estado),
            programa: solicitudBD[CONFIG.COLUMNAS.PROGRAMA] || solicitudBD.id_programa || "",
            rae: solicitudBD[CONFIG.COLUMNAS.RAE] || solicitudBD.id_rae || "",
            actividad: solicitudBD[CONFIG.COLUMNAS.ACTIVIDAD] || solicitudBD.id_actividad || "",
            solicitante: solicitudBD[CONFIG.COLUMNAS.SOLICITANTE] || solicitudBD.id_usuario_solicitante || "",
            aprobador: solicitudBD[CONFIG.COLUMNAS.APROBADOR] || solicitudBD.id_usuario_aprobador || "",
            observaciones: solicitudBD[CONFIG.COLUMNAS.OBSERVACIONES] || solicitudBD.observaciones || "",
            fecha_respuesta: solicitudBD[CONFIG.COLUMNAS.FECHA_RESPUESTA] || solicitudBD.fecha_respuesta || ""
        };
    },

    formatearFecha(fechaString) {
        if (!fechaString) return "Fecha no disponible";
        try {
            const fecha = new Date(fechaString);
            return fecha.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return fechaString;
        }
    },

    mostrarError(mensaje) {
        console.error("❌ Error:", mensaje);
        alert(`Error: ${mensaje}`);
    },

    mostrarExito(mensaje) {
        console.log("✅ Éxito:", mensaje);
        alert(`Éxito: ${mensaje}`);
    }
};

// ============================================================
//  FUNCIONES DE API - AJUSTADAS A TU BD
// ============================================================
const api = {
   // En tu archivo solicitudes.js, modifica la función api.listarSolicitudes():
async listarSolicitudes() {
    try {
        console.log("🔍 Solicitando datos de la API:", `${API}?accion=listar`);
        
        const response = await fetch(`${API}?accion=listar`);
        
        console.log("📡 Respuesta HTTP:", {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok
        });
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
        }
        
        const rawText = await response.text();
        console.log("📦 Texto crudo recibido:", rawText);
        
        let data;
        try {
            data = JSON.parse(rawText);
        } catch (e) {
            console.error("❌ Error parseando JSON:", e);
            console.error("Texto recibido:", rawText);
            throw new Error('Respuesta del servidor no es JSON válido');
        }
        
        console.log("📦 Datos parseados:", data);
        
        if (!Array.isArray(data)) {
            console.error("❌ La respuesta no es un array:", data);
            return [];
        }
        
        // Procesar cada solicitud con la función extractora
        const solicitudesProcesadas = data.map(s => 
            utilidades.extraerDatosSolicitud(s)
        );
        
        console.log(`✅ ${solicitudesProcesadas.length} solicitudes procesadas`);
        return solicitudesProcesadas;
        
    } catch (error) {
        console.error('❌ Error al cargar solicitudes:', error);
        utilidades.mostrarError(`No se pudieron cargar las solicitudes: ${error.message}`);
        return [];
    }
},

    async crearSolicitud(datos) {
        try {
            console.log("📤 Enviando solicitud:", datos);
            
            // Preparar datos según la estructura de tu BD
            const datosParaEnviar = {
                accion: 'crear',
                id_ficha: datos.ficha,
                id_programa: datos.programa,
                id_rae: datos.rae,
                id_actividad: datos.actividad || 0,
                observaciones: datos.observaciones,
                materiales: datos.materiales
            };

            const response = await fetch(API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datosParaEnviar)
            });

            const result = await response.json();
            console.log("📥 Respuesta del servidor:", result);
            
            if (result.success) {
                utilidades.mostrarExito('Solicitud creada correctamente');
                return result;
            } else {
                throw new Error(result.error || result.message || 'Error al crear la solicitud');
            }
        } catch (error) {
            console.error('❌ Error al crear solicitud:', error);
            utilidades.mostrarError(error.message);
            throw error;
        }
    },

    async cargarSelectores() {
        try {
            console.log("📚 Cargando datos para selectores...");
            
            // Cargar programas
            const responseProgramas = await fetch(`${API}?accion=programas`);
            if (responseProgramas.ok) {
                const programas = await responseProgramas.json();
                console.log("📋 Programas cargados:", programas);
                
                selectores.selectPrograma.innerHTML = '<option value="">Seleccionar programa</option>';
                if (Array.isArray(programas)) {
                    programas.forEach(programa => {
                        const option = document.createElement('option');
                        option.value = programa.id_programa;
                        option.textContent = `${programa.codigo_programa} - ${programa.nombre_programa}`;
                        selectores.selectPrograma.appendChild(option);
                    });
                }
            }

            // UN SOLO LISTENER para el cambio de programa
            selectores.selectPrograma.addEventListener('change', async function() {
                const programaId = this.value;
                if (!programaId) {
                    // Limpiar RAEs y fichas si no hay programa seleccionado
                    selectores.selectRae.innerHTML = '<option value="">Seleccionar RAE</option>';
                    selectores.selectFicha.innerHTML = '<option value="">Seleccionar ficha</option>';
                    return;
                }
                
                // Cargar RAEs y fichas en PARALELO
                try {
                    const [responseRaes, responseFichas] = await Promise.all([
                        fetch(`${API}?accion=raes&programa=${programaId}`),
                        fetch(`${API}?accion=fichas&programa=${programaId}`)
                    ]);

                    // Cargar RAEs
                    if (responseRaes.ok) {
                        const raes = await responseRaes.json();
                        selectores.selectRae.innerHTML = '<option value="">Seleccionar RAE</option>';
                        if (Array.isArray(raes)) {
                            raes.forEach(rae => {
                                const option = document.createElement('option');
                                option.value = rae.id_rae;
                                option.textContent = `${rae.codigo_rae} - ${rae.descripcion_rae}`;
                                selectores.selectRae.appendChild(option);
                            });
                        }
                    }

                    // Cargar Fichas
                    if (responseFichas.ok) {
                        const fichas = await responseFichas.json();
                        selectores.selectFicha.innerHTML = '<option value="">Seleccionar ficha</option>';
                        if (Array.isArray(fichas)) {
                            fichas.forEach(ficha => {
                                const option = document.createElement('option');
                                option.value = ficha.id_ficha;
                                option.textContent = `${ficha.numero_ficha} - ${ficha.jornada}`;
                                selectores.selectFicha.appendChild(option);
                            });
                        }
                    }
                    
                } catch (error) {
                    console.error('❌ Error al cargar RAEs/Fichas:', error);
                }
            });

            // Cargar materiales
            const responseMateriales = await fetch(`${API}?accion=materiales`);
            if (responseMateriales.ok) {
                const materiales = await responseMateriales.json();
                console.log("📦 Materiales cargados:", materiales.length);
                
                selectores.selectMaterial.innerHTML = '<option value="">Seleccionar material</option>';
                if (Array.isArray(materiales)) {
                    materiales.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.id_material;
                        option.textContent = `${material.nombre} (${material.codigo_inventario || 'Sin código'})`;
                        option.dataset.stock = material.stock_actual || 0;
                        option.dataset.unidad = material.unidad_medida;
                        selectores.selectMaterial.appendChild(option);
                    });
                }
            }

        } catch (error) {
            console.error('❌ Error cargando selectores:', error);
        }
    }
};

// ============================================================
//  FUNCIONES DE RENDERIZADO
// ============================================================
const render = {
    actualizarResumen() {
        const contadores = {
            pendiente: 0,
            aprobada: 0,
            entregada: 0,
            rechazada: 0
        };

        estadoApp.solicitudes.forEach(s => {
            if (contadores.hasOwnProperty(s.estado)) {
                contadores[s.estado]++;
            }
        });

        console.log("📊 Contadores:", contadores);

        if (selectores.resumenPendientes) selectores.resumenPendientes.textContent = contadores.pendiente;
        if (selectores.resumenAprobadas) selectores.resumenAprobadas.textContent = contadores.aprobada;
        if (selectores.resumenEntregadas) selectores.resumenEntregadas.textContent = contadores.entregada;
        if (selectores.resumenRechazadas) selectores.resumenRechazadas.textContent = contadores.rechazada;
    },

    actualizarFiltros() {
        const contadores = {
            pendiente: 0,
            aprobada: 0,
            entregada: 0,
            rechazada: 0
        };

        estadoApp.solicitudes.forEach(s => {
            if (contadores.hasOwnProperty(s.estado)) {
                contadores[s.estado]++;
            }
        });

        selectores.filtros.forEach(btn => {
            const filtro = btn.dataset.filtro;
            if (filtro === 'todas') {
                btn.textContent = `Todas (${estadoApp.solicitudes.length})`;
            } else {
                btn.textContent = `${CONFIG.LABELS[filtro]}s (${contadores[filtro]})`;
            }
        });
    },

    renderizarSolicitudes() {
        console.log("🎨 Renderizando solicitudes:", {
            total: estadoApp.solicitudes.length,
            filtro: estadoApp.filtroActivo,
            pagina: estadoApp.paginaActual
        });

        // Si no hay solicitudes
        if (estadoApp.solicitudes.length === 0) {
            selectores.contenedorCards.innerHTML = `
                <div class="col-span-full py-12 text-center">
                    <i data-lucide="file-text" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No hay solicitudes registradas</h3>
                    <p class="text-gray-500">Cree una nueva solicitud para comenzar</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        let solicitudesFiltradas;
        
        if (estadoApp.filtroActivo === 'todas') {
            solicitudesFiltradas = estadoApp.solicitudes;
        } else {
            solicitudesFiltradas = estadoApp.solicitudes.filter(
                s => s.estado === estadoApp.filtroActivo
            );
        }

        console.log("🔍 Solicitudes filtradas:", solicitudesFiltradas.length);

        if (solicitudesFiltradas.length === 0) {
            selectores.contenedorCards.innerHTML = `
                <div class="col-span-full py-12 text-center">
                    <i data-lucide="filter" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No hay solicitudes ${CONFIG.LABELS[estadoApp.filtroActivo]}s</h3>
                    <p class="text-gray-500">Intente con otro filtro</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        const inicio = (estadoApp.paginaActual - 1) * CONFIG.PAGE_SIZE;
        const fin = inicio + CONFIG.PAGE_SIZE;
        const paginaSolicitudes = solicitudesFiltradas.slice(inicio, fin);

        selectores.contenedorCards.innerHTML = '';

        paginaSolicitudes.forEach(solicitud => {
            const card = document.createElement('div');
            card.className = 'sol-card';
            card.dataset.id = solicitud.id;

            card.innerHTML = `
                <div class="sol-card-header">
                    <div class="sol-card-title-wrap">
                        <div class="sol-card-icon ${solicitud.estado}">
                            <i data-lucide="${CONFIG.ICONS[solicitud.estado]}"></i>
                        </div>
                        <div>
                            <div class="sol-card-title">Solicitud #${solicitud.id}</div>
                            <div class="sol-card-date">${solicitud.fecha}</div>
                        </div>
                    </div>

                    <span class="sol-badge ${solicitud.estado}">
                        ${CONFIG.LABELS[solicitud.estado]}
                    </span>
                </div>

                <div class="sol-card-body">
                    <div class="sol-card-row">
                        <i data-lucide="hash" class="sol-icon-muted"></i>
                        <span>Ficha: ${solicitud.ficha}</span>
                    </div>
                    
                    ${solicitud.rae ? `
                    <div class="sol-card-row">
                        <i data-lucide="target" class="sol-icon-muted"></i>
                        <span>RAE: ${solicitud.rae}</span>
                    </div>
                    ` : ''}
                    
                    ${solicitud.observaciones ? `
                    <div class="sol-card-row">
                        <i data-lucide="message-square" class="sol-icon-muted"></i>
                        <span class="truncate" title="${solicitud.observaciones}">
                            ${solicitud.observaciones.substring(0, 60)}${solicitud.observaciones.length > 60 ? '...' : ''}
                        </span>
                    </div>
                    ` : ''}
                    
                    ${solicitud.fecha_respuesta ? `
                    <div class="sol-card-row">
                        <i data-lucide="calendar-check" class="sol-icon-muted"></i>
                        <span>Respuesta: ${utilidades.formatearFecha(solicitud.fecha_respuesta)}</span>
                    </div>
                    ` : ''}
                </div>
            `;

            selectores.contenedorCards.appendChild(card);
        });

        lucide.createIcons();
        this.renderizarPaginacion(solicitudesFiltradas.length);
    },

    renderizarPaginacion(totalItems) {
        const totalPaginas = Math.ceil(totalItems / CONFIG.PAGE_SIZE);
        
        if (totalPaginas <= 1) {
            selectores.paginacion.innerHTML = '';
            return;
        }

        let paginacionHTML = '';

        // Botón anterior
        paginacionHTML += `
            <button class="sol-paginacion-btn ${estadoApp.paginaActual === 1 ? 'disabled' : ''}" 
                    ${estadoApp.paginaActual === 1 ? 'disabled' : ''}
                    onclick="paginacion.cambiarPagina(${estadoApp.paginaActual - 1})">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
        `;

        // Números de página
        for (let i = 1; i <= totalPaginas; i++) {
            if (i === 1 || i === totalPaginas || 
                (i >= estadoApp.paginaActual - 1 && i <= estadoApp.paginaActual + 1)) {
                paginacionHTML += `
                    <button class="sol-paginacion-btn ${estadoApp.paginaActual === i ? 'active' : ''}" 
                            onclick="paginacion.cambiarPagina(${i})">
                        ${i}
                    </button>
                `;
            } else if (i === estadoApp.paginaActual - 2 || i === estadoApp.paginaActual + 2) {
                paginacionHTML += '<span class="px-2 text-gray-400">...</span>';
            }
        }

        // Botón siguiente
        paginacionHTML += `
            <button class="sol-paginacion-btn ${estadoApp.paginaActual === totalPaginas ? 'disabled' : ''}" 
                    ${estadoApp.paginaActual === totalPaginas ? 'disabled' : ''}
                    onclick="paginacion.cambiarPagina(${estadoApp.paginaActual + 1})">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        `;

        selectores.paginacion.innerHTML = paginacionHTML;
        lucide.createIcons();
    },

    renderizarMateriales() {
        if (estadoApp.materialesSeleccionados.length === 0) {
            selectores.listaMateriales.innerHTML = `
                <div class="text-center text-muted-foreground py-8">
                    <i data-lucide="package" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>No hay materiales agregados</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="space-y-2">
                <div class="flex justify-between text-sm font-medium text-gray-500 pb-2 border-b">
                    <span class="flex-1">Material</span>
                    <span class="w-24 text-center">Cantidad</span>
                    <span class="w-16 text-center">Acciones</span>
                </div>
        `;

        estadoApp.materialesSeleccionados.forEach((material, index) => {
            html += `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <div class="font-medium">${material.nombre}</div>
                        <div class="text-sm text-gray-500">${material.unidad} • Stock: ${material.stock}</div>
                    </div>
                    <div class="w-24 text-center">
                        <span class="font-semibold">${material.cantidad}</span>
                    </div>
                    <div class="w-16 text-center">
                        <button type="button" onclick="materiales.eliminarMaterial(${index})" 
                                class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        // Total
        const totalMateriales = estadoApp.materialesSeleccionados.reduce((sum, m) => sum + m.cantidad, 0);
        html += `
            <div class="pt-2 border-t">
                <div class="flex justify-between font-medium text-gray-700">
                    <span>Total materiales:</span>
                    <span>${totalMateriales} unidades</span>
                </div>
            </div>
        </div>`;

        selectores.listaMateriales.innerHTML = html;
        lucide.createIcons();
    }
};

// ============================================================
//  GESTIÓN DE MATERIALES
// ============================================================
const materiales = {
    agregarMaterial() {
        const materialId = selectores.selectMaterial.value;
        const cantidad = parseInt(selectores.inputCantidad.value);
        
        if (!materialId) {
            utilidades.mostrarError('Seleccione un material');
            return;
        }
        
        if (!cantidad || cantidad < 1) {
            utilidades.mostrarError('Ingrese una cantidad válida (mínimo 1)');
            selectores.inputCantidad.focus();
            return;
        }

        const option = selectores.selectMaterial.selectedOptions[0];
        const stock = parseInt(option.dataset.stock) || 0;
        const unidad = option.dataset.unidad || 'UND';
        
        if (cantidad > stock) {
            utilidades.mostrarError(`Stock insuficiente. Disponible: ${stock} ${unidad}`);
            selectores.inputCantidad.value = stock;
            selectores.inputCantidad.focus();
            return;
        }

        // Verificar si ya existe
        const existe = estadoApp.materialesSeleccionados.find(m => m.id == materialId);
        if (existe) {
            if (confirm('Este material ya fue agregado. ¿Desea actualizar la cantidad?')) {
                existe.cantidad = cantidad;
                render.renderizarMateriales();
            }
            return;
        }

        estadoApp.materialesSeleccionados.push({
            id: materialId,
            nombre: option.textContent,
            cantidad: cantidad,
            stock: stock,
            unidad: unidad
        });

        // Limpiar campos
        selectores.selectMaterial.value = '';
        selectores.inputCantidad.value = '1';
        
        // Renderizar lista
        render.renderizarMateriales();
        
        utilidades.mostrarExito('Material agregado correctamente');
    },

    eliminarMaterial(index) {
        if (confirm('¿Está seguro de eliminar este material?')) {
            estadoApp.materialesSeleccionados.splice(index, 1);
            render.renderizarMateriales();
            utilidades.mostrarExito('Material eliminado');
        }
    },

    limpiarMateriales() {
        if (estadoApp.materialesSeleccionados.length > 0) {
            if (!confirm('¿Está seguro de limpiar todos los materiales?')) {
                return;
            }
        }
        estadoApp.materialesSeleccionados = [];
        render.renderizarMateriales();
    }
};

// ============================================================
//  GESTIÓN DE PAGINACIÓN
// ============================================================
const paginacion = {
    cambiarPagina(nuevaPagina) {
        const totalSolicitudes = estadoApp.filtroActivo === 'todas' 
            ? estadoApp.solicitudes.length 
            : estadoApp.solicitudes.filter(s => s.estado === estadoApp.filtroActivo).length;
        
        const totalPaginas = Math.ceil(totalSolicitudes / CONFIG.PAGE_SIZE);
        
        if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
        
        estadoApp.paginaActual = nuevaPagina;
        render.renderizarSolicitudes();
        
        // Scroll suave al principio
        window.scrollTo({
            top: selectores.contenedorCards.offsetTop - 100,
            behavior: 'smooth'
        });
    }
};

// ============================================================
//  GESTIÓN DEL MODAL
// ============================================================
const modal = {
    abrir() {
        selectores.modal.classList.add('sol-modal-show');
        selectores.paso1.classList.remove('hidden');
        selectores.paso2.classList.add('hidden');
        this.limpiarFormulario();
        
        // Enfocar el primer campo
        setTimeout(() => {
            selectores.selectPrograma.focus();
        }, 100);
    },

    cerrar() {
        if (estadoApp.materialesSeleccionados.length > 0 || 
            selectores.textareaObservaciones.value.trim() !== '' ||
            selectores.selectPrograma.value !== '') {
            
            if (!confirm('¿Está seguro de cerrar? Se perderán los datos no guardados.')) {
                return;
            }
        }
        
        selectores.modal.classList.remove('sol-modal-show');
        this.limpiarFormulario();
    },

    limpiarFormulario() {
        estadoApp.datosFormulario = {
            programa: "",
            rae: "",
            ficha: "",
            actividad: "",
            observaciones: ""
        };
        estadoApp.materialesSeleccionados = [];
        
        selectores.formNueva.reset();
        materiales.limpiarMateriales();
    },

    validarPaso1() {
        if (!selectores.selectPrograma.value) {
            utilidades.mostrarError('Seleccione un programa');
            selectores.selectPrograma.focus();
            return false;
        }
        
        if (!selectores.selectRae.value) {
            utilidades.mostrarError('Seleccione un RAE');
            selectores.selectRae.focus();
            return false;
        }
        
        if (!selectores.selectFicha.value) {
            utilidades.mostrarError('Seleccione una ficha');
            selectores.selectFicha.focus();
            return false;
        }

        // Guardar datos del paso 1
        estadoApp.datosFormulario = {
            programa: selectores.selectPrograma.value,
            rae: selectores.selectRae.value,
            ficha: selectores.selectFicha.value,
            actividad: selectores.selectFicha.value, // Usamos ficha como actividad si no hay campo específico
            observaciones: selectores.textareaObservaciones.value.trim()
        };

        return true;
    },

    async enviarSolicitud() {
        if (estadoApp.materialesSeleccionados.length === 0) {
            utilidades.mostrarError('Debe agregar al menos un material');
            selectores.selectMaterial.focus();
            return;
        }

        // Mostrar confirmación
        if (!confirm('¿Está seguro de crear esta solicitud?')) {
            return;
        }

        try {
            // Deshabilitar botón para evitar doble envío
            selectores.btnGuardar.disabled = true;
            selectores.btnGuardar.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Procesando...';
            lucide.createIcons();

            const datosCompletos = {
                ...estadoApp.datosFormulario,
                materiales: estadoApp.materialesSeleccionados.map(m => ({
                    id_material: m.id,
                    cantidad_solicitada: m.cantidad
                }))
            };

            await api.crearSolicitud(datosCompletos);
            
            // Recargar las solicitudes
            await app.cargarSolicitudes();
            
            // Cerrar modal
            this.cerrar();
            
        } catch (error) {
            console.error('Error al enviar solicitud:', error);
        } finally {
            // Rehabilitar botón
            selectores.btnGuardar.disabled = false;
            selectores.btnGuardar.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i> Crear Solicitud';
            lucide.createIcons();
        }
    }
};

// ============================================================
//  EVENT LISTENERS
// ============================================================
const eventos = {
    inicializar() {
        // Botón nueva solicitud
        if (selectores.btnNueva) {
            selectores.btnNueva.addEventListener('click', () => modal.abrir());
        }
        
        // Cerrar modal
        if (selectores.btnCerrarModal) {
            selectores.btnCerrarModal.addEventListener('click', () => modal.cerrar());
        }
        
        if (selectores.btnCancelar) {
            selectores.btnCancelar.addEventListener('click', () => modal.cerrar());
        }
        
        // Navegación entre pasos
        if (selectores.btnPaso2) {
            selectores.btnPaso2.addEventListener('click', () => {
                if (modal.validarPaso1()) {
                    selectores.paso1.classList.add('hidden');
                    selectores.paso2.classList.remove('hidden');
                    selectores.selectMaterial.focus();
                }
            });
        }
        
        if (selectores.btnVolver) {
            selectores.btnVolver.addEventListener('click', () => {
                selectores.paso2.classList.add('hidden');
                selectores.paso1.classList.remove('hidden');
                selectores.selectPrograma.focus();
            });
        }
        
        // Agregar material
        if (selectores.btnAgregarMaterial) {
            selectores.btnAgregarMaterial.addEventListener('click', () => materiales.agregarMaterial());
        }
        
        if (selectores.inputCantidad) {
            selectores.inputCantidad.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    materiales.agregarMaterial();
                }
            });
        }
        
        // Enviar formulario
        if (selectores.formNueva) {
            selectores.formNueva.addEventListener('submit', async (e) => {
                e.preventDefault();
                await modal.enviarSolicitud();
            });
        }
        
        // Filtros
        if (selectores.filtros.length > 0) {
            selectores.filtros.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remover clase activa de todos
                    selectores.filtros.forEach(b => b.classList.remove('sol-filtro-btn-activo'));
                    
                    // Agregar al botón clickeado
                    btn.classList.add('sol-filtro-btn-activo');
                    
                    // Actualizar filtro y renderizar
                    estadoApp.filtroActivo = btn.dataset.filtro;
                    estadoApp.paginaActual = 1;
                    render.renderizarSolicitudes();
                });
            });
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && selectores.modal && selectores.modal.classList.contains('sol-modal-show')) {
                modal.cerrar();
            }
        });

        // Click fuera del modal para cerrar
        if (selectores.modal) {
            selectores.modal.addEventListener('click', (e) => {
                if (e.target === selectores.modal) {
                    modal.cerrar();
                }
            });
        }
    }
};

// ============================================================
//  APLICACIÓN PRINCIPAL
// ============================================================
const app = {
    async inicializar() {
        console.log("🚀 Inicializando módulo de solicitudes...");
        
        // Mostrar estado de carga
        selectores.contenedorCards.innerHTML = `
            <div class="col-span-full py-12 text-center">
                <i data-lucide="loader" class="w-12 h-12 text-blue-300 animate-spin mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Cargando solicitudes</h3>
                <p class="text-gray-500">Obteniendo datos de la base de datos...</p>
            </div>
        `;
        lucide.createIcons();
        
        // Cargar datos iniciales
        await this.cargarSolicitudes();
        await api.cargarSelectores();
        
        // Inicializar eventos
        eventos.inicializar();
        
        console.log("✅ Módulo inicializado");
    },

    async cargarSolicitudes() {
        estadoApp.solicitudes = await api.listarSolicitudes();
        console.log(`✅ Cargadas ${estadoApp.solicitudes.length} solicitudes`);
        
        render.actualizarResumen();
        render.actualizarFiltros();
        render.renderizarSolicitudes();
    }
};

// ============================================================
//  INICIALIZACIÓN
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    app.inicializar();
});

// Exportar funciones para acceso global
window.paginacion = paginacion;
window.materiales = materiales;