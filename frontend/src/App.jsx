import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'
import { AuthProvider, useAuth } from './auth/AuthContext'
import LayoutPrincipal from './layout/LayoutPrincipal'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import ControlAcceso from './pages/ControlAcceso'
import SociosMembresias from './pages/SociosMembresias'
import Congelamientos from './pages/Congelamientos'
import Ajustes from './pages/Ajustes'

function RutaProtegida({ children }) {
  const { autenticado } = useAuth()
  if (!autenticado) {
    return <Navigate to="/login" replace />
  }
  return children
}

function RutaLogin() {
  const { autenticado } = useAuth()
  if (autenticado) {
    return <Navigate to="/" replace />
  }
  return <Login />
}

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<RutaLogin />} />

          <Route
            element={
              <RutaProtegida>
                <LayoutPrincipal />
              </RutaProtegida>
            }
          >
            <Route index element={<Dashboard />} />
            <Route path="acceso" element={<ControlAcceso />} />
            <Route path="socios" element={<SociosMembresias />} />
            <Route path="congelamientos" element={<Congelamientos />} />
            <Route path="ajustes" element={<Ajustes />} />
          </Route>

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  )
}
