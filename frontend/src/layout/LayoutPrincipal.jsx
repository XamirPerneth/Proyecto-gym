import { useState } from 'react'
import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import {
  Clock,
  Dumbbell,
  LayoutDashboard,
  LogOut,
  Menu,
  ScanLine,
  Settings,
  Snowflake,
  Users,
  X,
} from 'lucide-react'
import { useAuth } from '../auth/AuthContext'

const NAVEGACION = [
  { a: '/', etiqueta: 'Inicio / Dashboard', Icono: LayoutDashboard, fin: true, id: 'nav-dashboard' },
  { a: '/acceso', etiqueta: 'Control de Acceso', Icono: ScanLine, id: 'nav-acceso' },
  { a: '/socios', etiqueta: 'Socios y Membresías', Icono: Users, id: 'nav-socios' },
  { a: '/congelamientos', etiqueta: 'Congelamientos', Icono: Snowflake, id: 'nav-congelamientos' },
  { a: '/ajustes', etiqueta: 'Ajustes del Gimnasio', Icono: Settings, id: 'nav-ajustes' },
]

export default function LayoutPrincipal() {
  const { usuario, cerrarSesion, ajustesClub } = useAuth()
  const navigate = useNavigate()
  const [menuAbierto, setMenuAbierto] = useState(false)

  const iniciales = (usuario?.nombre ?? 'AD')
    .split(' ')
    .map((p) => p[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()

  async function salir() {
    await cerrarSesion()
    navigate('/login', { replace: true })
  }

  return (
    <div className="min-h-screen bg-[#F8FAFC] text-zinc-900 lg:flex">
      {/* Cabecera móvil */}
      <header className="flex items-center justify-between border-b border-[#E2E8F0] bg-white px-4 py-3 lg:hidden">
        <div className="flex items-center gap-2.5">
          <span className="grid h-9 w-9 place-items-center rounded-xl bg-[#EA580C] text-white shadow-sm">
            <Dumbbell className="h-5 w-5" />
          </span>
          <span className="font-bold text-zinc-900">{ajustesClub.nombreGimnasio}</span>
        </div>
        <button
          type="button"
          onClick={() => setMenuAbierto(!menuAbierto)}
          className="rounded-lg border border-[#E2E8F0] p-2 text-zinc-700"
        >
          {menuAbierto ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
        </button>
      </header>

      {/* Sidebar lateral fijo */}
      <aside
        className={`fixed inset-y-0 left-0 z-40 flex w-72 flex-col justify-between border-r border-[#E2E8F0] bg-[#18181B] text-white transition-transform lg:static lg:translate-x-0 ${
          menuAbierto ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div>
          {/* Logo del gimnasio */}
          <div className="flex items-center gap-3 border-b border-white/10 px-6 py-5">
            <span className="grid h-11 w-11 place-items-center rounded-xl bg-[#EA580C] text-white shadow-lg shadow-[#EA580C]/30">
              <Dumbbell className="h-6 w-6" />
            </span>
            <div>
              <p className="text-base font-extrabold tracking-tight text-white">
                {ajustesClub.nombreGimnasio}
              </p>
              <p className="text-xs font-medium text-zinc-400">Recepción & Control SaaS</p>
            </div>
          </div>

          {/* Menú de 5 secciones */}
          <nav className="mt-6 space-y-1.5 px-4">
            <p className="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
              Operación del club
            </p>
            {NAVEGACION.map(({ a, etiqueta, Icono, fin, id }) => (
              <NavLink
                key={a}
                to={a}
                end={fin}
                id={id}
                onClick={() => setMenuAbierto(false)}
                className={({ isActive }) =>
                  `flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition ${
                    isActive
                      ? 'bg-[#EA580C] text-white shadow-md shadow-[#EA580C]/25'
                      : 'text-zinc-300 hover:bg-white/5 hover:text-white'
                  }`
                }
              >
                <Icono className="h-5 w-5 shrink-0" />
                <span>{etiqueta}</span>
              </NavLink>
            ))}
          </nav>
        </div>

        {/* Perfil del recepcionista logueado y cerrar sesión */}
        <div className="border-t border-white/10 p-4">
          <div className="mb-3 flex items-center gap-3 rounded-xl bg-white/5 p-3">
            <div className="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#EA580C]/20 text-sm font-bold text-[#EA580C] ring-1 ring-[#EA580C]/40">
              {iniciales}
            </div>
            <div className="min-w-0 flex-1">
              <p className="truncate text-sm font-semibold text-white">
                {usuario?.nombre ?? 'Administrador'}
              </p>
              <p className="truncate text-xs text-zinc-400">
                {usuario?.rol ?? 'Recepción'} · {usuario?.email}
              </p>
            </div>
          </div>

          <button
            id="btn-cerrar-sesion"
            type="button"
            onClick={salir}
            className="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs font-semibold text-zinc-200 transition hover:border-rose-500/40 hover:bg-rose-500/15 hover:text-rose-200"
          >
            <LogOut className="h-4 w-4" />
            Cerrar sesión
          </button>
        </div>
      </aside>

      {/* Contenido principal */}
      <div className="flex flex-1 flex-col lg:min-w-0">
        {ajustesClub.horaSimulada && (
          <div className="flex flex-wrap items-center justify-between gap-2 bg-amber-500 px-6 py-2 text-xs font-semibold text-zinc-950">
            <div className="flex items-center gap-2">
              <Clock className="h-4 w-4" />
              <span>
                MODO DEMOSTRACIÓN ACTIVO: El reloj del gimnasio está fijado en{' '}
                <strong>{ajustesClub.horaSimulada}</strong> para pruebas de horario en el torniquete.
              </span>
            </div>
            <NavLink to="/ajustes" className="underline hover:opacity-80">
              Cambiar o volver a hora real →
            </NavLink>
          </div>
        )}

        <main className="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-8">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
