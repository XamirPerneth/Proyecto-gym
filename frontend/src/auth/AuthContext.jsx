import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import { api, CLAVE_TOKEN, CLAVE_USUARIO, registrarManejadorNoAutorizado } from '../api/cliente'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [usuario, setUsuario] = useState(() => {
    try {
      const guardado = localStorage.getItem(CLAVE_USUARIO)
      return guardado ? JSON.parse(guardado) : null
    } catch {
      return null
    }
  })
  const [ajustesClub, setAjustesClub] = useState({
    nombreGimnasio: 'Gym Control Club',
    horaSimulada: null,
  })

  const limpiarSesion = useCallback(() => {
    localStorage.removeItem(CLAVE_TOKEN)
    localStorage.removeItem(CLAVE_USUARIO)
    setUsuario(null)
  }, [])

  const refrescarAjustesClub = useCallback(async () => {
    if (!localStorage.getItem(CLAVE_TOKEN)) return
    try {
      const datos = await api.obtenerAjustes()
      setAjustesClub({
        nombreGimnasio: datos.nombreGimnasio ?? 'Gym Control Club',
        horaSimulada: datos.horaSimulada ?? null,
      })
    } catch {
      // Ignorar si falla la carga inicial
    }
  }, [])

  useEffect(() => {
    registrarManejadorNoAutorizado(limpiarSesion)
  }, [limpiarSesion])

  useEffect(() => {
    if (usuario) {
      refrescarAjustesClub()
    }
  }, [usuario, refrescarAjustesClub])

  async function iniciarSesion(email, password) {
    const sesion = await api.iniciarSesion(email, password)
    localStorage.setItem(CLAVE_TOKEN, sesion.token)
    localStorage.setItem(CLAVE_USUARIO, JSON.stringify(sesion.usuario))
    setUsuario(sesion.usuario)
    return sesion.usuario
  }

  async function cerrarSesion() {
    try {
      await api.cerrarSesion()
    } catch {
      // Ignorar errores de red al salir
    } finally {
      limpiarSesion()
    }
  }

  return (
    <AuthContext.Provider
      value={{
        usuario,
        autenticado: Boolean(usuario && localStorage.getItem(CLAVE_TOKEN)),
        iniciarSesion,
        cerrarSesion,
        ajustesClub,
        setAjustesClub,
        refrescarAjustesClub,
      }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  return useContext(AuthContext)
}
