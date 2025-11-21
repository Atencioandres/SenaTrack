// Funciones globales para el sistema SenaTrack

// Verificar si hay un usuario autenticado
function verificarAutenticacion(tipoRequerido = null) {
  const usuarioActual = localStorage.getItem("usuarioActual")

  if (!usuarioActual) {
    window.location.href = "login.html"
    return false
  }

  if (tipoRequerido) {
    const usuario = JSON.parse(usuarioActual)
    if (usuario.tipoUsuario !== tipoRequerido) {
      alert("No tienes permisos para acceder a esta página")
      cerrarSesion()
      return false
    }
  } 

  return true
}

// Cerrar sesión
function cerrarSesion() {
  localStorage.removeItem("usuarioActual")
  window.location.href = "login.html"
}

// Inicializar datos de ejemplo si no existen
function inicializarDatosEjemplo() {
  const usuarios = localStorage.getItem("usuarios")

  if (!usuarios) {
    const usuariosEjemplo = [
      {
        id: "1",
        tipoUsuario: "admin",
        nombre: "Carlos Torres",
        usuario: "admin",
        correo: "admin@sena.edu.co",
        contrasena: "admin123",
        fechaRegistro: new Date().toISOString(),
      },
      {
        id: "2",
        tipoUsuario: "bienestar",
        nombre: "Tatiana Morales",
        usuario: "tmorales",
        correo: "tmorales@sena.edu.co",
        contrasena: "bienestar123",
        especialidad: "Psicología",
        fechaRegistro: new Date().toISOString(),
      },
      {
        id: "3",
        tipoUsuario: "aprendiz",
        nombre: "Juan Pérez",
        usuario: "jperez",
        correo: "jperez@sena.edu.co",
        contrasena: "aprendiz123",
        programa: "Análisis y Desarrollo de Software",
        ficha: "2924030",
        telefono: "3104560001",
        fechaRegistro: new Date().toISOString(),
      },
    ]

    localStorage.setItem("usuarios", JSON.stringify(usuariosEjemplo))

    // Crear algunos comparendos de ejemplo
    const comparendosEjemplo = [
      {
        id: "1",
        idAprendiz: "3",
        idBienestar: "2",
        tipoInforme: "Conducta",
        descripcion: "Llegó tarde reiteradamente a clase",
        fecha: new Date().toISOString().split("T")[0],
        fechaRegistro: new Date().toISOString(),
      },
    ]

    localStorage.setItem("comparendos", JSON.stringify(comparendosEjemplo))
  }
}

// Inicializar datos al cargar la página
if (typeof window !== "undefined") {
  inicializarDatosEjemplo()
}

// Función para formatear fechas
function formatearFecha(fecha) {
  const opciones = { year: "numeric", month: "long", day: "numeric" }
  return new Date(fecha).toLocaleDateString("es-ES", opciones)
}

// Función para obtener el usuario actual
function obtenerUsuarioActual() {
  const usuario = localStorage.getItem("usuarioActual")
  return usuario ? JSON.parse(usuario) : null
}

// Función para validar correo electrónico
function validarCorreo(correo) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return regex.test(correo)
}

// Función para validar contraseña (mínimo 6 caracteres)
function validarContrasena(contrasena) {
  return contrasena.length >= 6
}

// Función helper: hacer login contra el backend (Api/login.php)
// Devuelve la respuesta JSON y guarda el usuario en localStorage como 'usuarioActual' si es exitoso.
async function loginServer(usuario, contrasena, tipo_usuario) {
  try {
    const resp = await fetch('Api/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ usuario, contrasena, tipo_usuario })
    })

    const data = await resp.json()
    if (data.success && data.user) {
      // Mapear campos a la forma que usa el frontend (tipoUsuario)
      const user = Object.assign({}, data.user, { tipoUsuario: data.user.tipo_usuario })
      localStorage.setItem('usuarioActual', JSON.stringify(user))
    }
    return data
  } catch (err) {
    return { success: false, message: 'Error de red' }
  }
}

// Exportar funciones para uso global
if (typeof module !== "undefined" && module.exports) {
  module.exports = {
    verificarAutenticacion,
    cerrarSesion,
    inicializarDatosEjemplo,
    formatearFecha,
    obtenerUsuarioActual,
    validarCorreo,
    validarContrasena,
  }
}
