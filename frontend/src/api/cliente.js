const BASE = '/api/v1'
export const CLAVE_TOKEN = 'gym_token'
export const CLAVE_USUARIO = 'gym_usuario'

/** Error con el mensaje exacto devuelto por el dominio (422/404/401) o por la validación. */
export class ErrorApi extends Error {
  constructor(mensaje, estado) {
    super(mensaje)
    this.estado = estado
  }
}

let manejadorNoAutorizado = null
export function registrarManejadorNoAutorizado(fn) {
  manejadorNoAutorizado = fn
}

export function obtenerToken() {
  return localStorage.getItem(CLAVE_TOKEN)
}

async function solicitar(ruta, opciones = {}) {
  const token = obtenerToken()
  const cabeceras = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
    ...(opciones.headers ?? {}),
  }

  let respuesta
  try {
    respuesta = await fetch(`${BASE}${ruta}`, {
      ...opciones,
      headers: cabeceras,
    })
  } catch {
    throw new ErrorApi('No hay conexión con el servidor del gimnasio.', 0)
  }

  const cuerpo = await respuesta.json().catch(() => ({}))

  if (respuesta.status === 401 && ruta !== '/login') {
    localStorage.removeItem(CLAVE_TOKEN)
    localStorage.removeItem(CLAVE_USUARIO)
    if (manejadorNoAutorizado) manejadorNoAutorizado()
  }

  if (!respuesta.ok) {
    throw new ErrorApi(cuerpo.message ?? 'Ocurrió un error inesperado.', respuesta.status)
  }

  return cuerpo.data
}

const post = (ruta, datos) => solicitar(ruta, { method: 'POST', body: JSON.stringify(datos ?? {}) })
const put = (ruta, datos) => solicitar(ruta, { method: 'PUT', body: JSON.stringify(datos ?? {}) })

export const api = {
  iniciarSesion: (email, password) => post('/login', { email, password }),
  obtenerPerfil: () => solicitar('/me'),
  cerrarSesion: () => post('/logout'),

  obtenerDashboard: () => solicitar('/dashboard'),
  validarCheckIn: (documento) => post('/check-in', { documento }),
  listarAccesos: (limite = 10) => solicitar(`/accesos?limite=${limite}`),

  listarSocios: () => solicitar('/socios'),
  registrarSocio: (datos) => post('/socios', datos),
  listarPlanes: () => solicitar('/planes'),

  asignarMembresia: (datos) => post('/membresias', datos),
  congelarMembresia: (id, dias) => post(`/membresias/${id}/congelar`, { dias }),
  reactivarMembresia: (id) => post(`/membresias/${id}/reactivar`),

  obtenerAjustes: () => solicitar('/ajustes'),
  actualizarAjustes: (datos) => put('/ajustes', datos),
}

export const formatearPesos = (valor) =>
  new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(valor ?? 0)
