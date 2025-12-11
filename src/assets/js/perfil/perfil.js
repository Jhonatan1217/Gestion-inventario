// -------------------------------
// Abrir popup principal (Editar perfil)
// -------------------------------
const btnEditarPerfil = document.getElementById("btnEditarPerfil");
const modalEditarPerfil = document.getElementById("modalEditarPerfil");
const cerrarEditarPerfil = document.getElementById("cerrarEditarPerfil");
const cancelarEditarPerfil = document.getElementById("cancelarEditarPerfil");

if (btnEditarPerfil) {
    btnEditarPerfil.onclick = () => modalEditarPerfil.classList.remove("hidden");
}

if (cerrarEditarPerfil && cancelarEditarPerfil) {
    cerrarEditarPerfil.onclick = cancelarEditarPerfil.onclick = () =>
        modalEditarPerfil.classList.add("hidden");
}



// ---------------------------------------------------------
// Clic en avatar → activar input de carga de foto
// ---------------------------------------------------------
const avatarClick = document.getElementById("avatarClick");
const inputFoto = document.getElementById("inputFoto");

if (avatarClick && inputFoto) {
    avatarClick.onclick = () => inputFoto.click();
}



// ---------------------------------------------------------
// Vista previa automática cuando se selecciona una imagen
// ---------------------------------------------------------
if (inputFoto) {
    inputFoto.addEventListener("change", function () {
        const archivo = this.files[0];
        if (!archivo) return;

        if (archivo.size > 2 * 1024 * 1024) {
            alert("La imagen supera los 2MB.");
            return;
        }

        const lector = new FileReader();
        lector.onload = function (e) {
            avatarClick.style.backgroundImage = `url('${e.target.result}')`;
            avatarClick.style.backgroundSize = "cover";
            avatarClick.style.backgroundPosition = "center";
            avatarClick.textContent = "";
        };
        lector.readAsDataURL(archivo);
    });
}



// =====================================================
// MODAL — CAMBIO DE CONTRASEÑA
// =====================================================
const modalPassword = document.getElementById("modalPassword");
const abrirCambioPass = document.getElementById("abrirCambioPass");
const cerrarPassword = document.getElementById("cerrarPassword");
const cancelarPassword = document.getElementById("cancelarPassword");

if (abrirCambioPass) {
    abrirCambioPass.onclick = () => modalPassword.classList.remove("hidden");
}

if (cerrarPassword && cancelarPassword) {
    cerrarPassword.onclick = cancelarPassword.onclick = () =>
        modalPassword.classList.add("hidden");
}



// =====================================================
// Cerrar modales clickeando fuera del contenedor
// =====================================================
window.addEventListener("click", function (e) {
    if (e.target === modalEditarPerfil) {
        modalEditarPerfil.classList.add("hidden");
    }
    if (e.target === modalPassword) {
        modalPassword.classList.add("hidden");
    }
});
