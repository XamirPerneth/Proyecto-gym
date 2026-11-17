import { useCallback, useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import {
  Activity,
  ArrowUpRight,
  BarChart3,
  DollarSign,
  ScanLine,
  Settings,
  Snowflake,
  UserPlus,
  Users,
} from 'lucide-react'
import { api, formatearPesos } from '../api/cliente'
import { useAuth } from '../auth/AuthContext'
import Aviso from '../components/Aviso'

export default function Dashboard() {
  const { usuario } = useAuth()
  const [resumen, setResumen] = useState(null)
  const [aviso, setAviso] = useState(null)
  const [bannerOculto, setBannerOculto] = useState(false)

  const cargar = useCallback(async () => {
    try {
      setResumen(await api.obtenerDashboard())
    } catch (err) {
      setAviso({ tipo: 'error', texto: err.message })
    }
  }, [])

  useEffect(() => {
    cargar()
  }, [cargar])

  const franjas = resumen?.ocupacionPorFranja ?? []
  const maxAccesos = Math.max(4, ...franjas.map((f) => f.accesos))

  return (
    <div className="space-y-8">
      <Aviso aviso={aviso} onCerrar={() => setAviso(null)} />

      {/* Banner superior con fotografía del gimnasio */}
      <div className="relative overflow-hidden rounded-3xl border border-[#E2E8F0] bg-[#18181B] text-white shadow-lg">
        {!bannerOculto && (
          <img
            src="/images/dashboard-banner.jpg"
            alt="Piso de entrenamiento del gimnasio"
            onError={() => setBannerOculto(true)}
            className="absolute inset-0 h-full w-full object-cover object-center opacity-55"
          />
        )}
        <div className="absolute inset-0 bg-gradient-to-r from-[#18181B] via-[#18181B]/85 to-transparent" />

        <div className="relative z-10 flex flex-col justify-between gap-6 p-8 sm:flex-row sm:items-center sm:p-10">
          <div className="max-w-xl space-y-2">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-[#EA580C]/20 px-3 py-1 text-xs font-semibold text-orange-300 ring-1 ring-[#EA580C]/40">
              Panel Operativo en Tiempo Real
            </span>
            <h1 className="text-3xl font-extrabold tracking-tight sm:text-4xl">
              ¡Hola, {usuario?.nombre?.split(' ')[0] ?? 'Admin'}!
            </h1>
            <p className="text-sm text-zinc-300">
              Supervisa el flujo de ingreso en el torniquete, el estado de las membresías activas y la
              facturación estimada de <strong>{resumen?.nombreGimnasio ?? 'Gym Control Club'}</strong>.
            </p>
          </div>

          <div className="flex flex-wrap gap-3">
            <Link to="/acceso" className="boton">
              <ScanLine className="h-4 w-4" />
              Abrir Torniquete
            </Link>
            <Link
              to="/socios"
              className="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20"
            >
              <UserPlus className="h-4 w-4" />
              Gestionar Socios
            </Link>
          </div>
        </div>
      </div>

      {/* 4 Tarjetas de KPIs */}
      <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <TarjetaKpi
          titulo="Socios Activos"
          valor={resumen?.sociosActivos ?? '—'}
          detalle="Con membresía vigente hoy"
          Icono={Users}
          acento="bg-orange-50 text-[#EA580C]"
        />
        <TarjetaKpi
          titulo="Accesos de Hoy"
          valor={resumen?.accesosHoy ?? '—'}
          detalle={`${resumen?.accesosPermitidosHoy ?? 0} permitidos · ${resumen?.accesosDenegadosHoy ?? 0} denegados`}
          Icono={Activity}
          acento="bg-emerald-50 text-emerald-600"
        />
        <TarjetaKpi
          titulo="Membresías Congeladas"
          valor={resumen?.membresiasCongeladas ?? '—'}
          detalle="Por incapacidad o viaje (7–30 días)"
          Icono={Snowflake}
          acento="bg-sky-50 text-sky-600"
        />
        <TarjetaKpi
          titulo="Ingresos Estimados"
          valor={resumen ? formatearPesos(resumen.ingresosEstimados) : '—'}
          detalle="Suma de planes vigentes"
          Icono={DollarSign}
          acento="bg-amber-50 text-amber-600"
        />
      </div>

      {/* Gráfico de ocupación por franja horaria + Accesos directos */}
      <div className="grid gap-6 lg:grid-cols-12">
        <section className="tarjeta lg:col-span-8">
          <div className="flex flex-wrap items-center justify-between gap-2">
            <div>
              <h2 className="flex items-center gap-2 text-lg font-bold text-zinc-900">
                <BarChart3 className="h-5 w-5 text-[#EA580C]" />
                Ocupación y Flujo por Franja Horaria (Hoy)
              </h2>
              <p className="text-xs text-slate-500">
                Ingresos permitidos registrados hoy en la tabla <code className="font-mono">registros_acceso</code>
              </p>
            </div>
            <span className="rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
              Plan Básico: 06:00–16:00 · Plan Full: 05:00–23:00
            </span>
          </div>

          <div className="mt-8 grid grid-cols-9 items-end gap-3 pt-4" style={{ height: '210px' }}>
            {franjas.map((f) => {
              const alturaPorcentaje = Math.max(12, Math.round((f.accesos / maxAccesos) * 100))
              return (
                <div key={f.franja} className="flex h-full flex-col items-center justify-end gap-2">
                  <span className="text-xs font-bold text-zinc-700">{f.accesos}</span>
                  <div className="flex h-36 w-full items-end rounded-xl bg-slate-100 p-1">
                    <div
                      style={{ height: `${alturaPorcentaje}%` }}
                      className={`w-full rounded-lg transition-all duration-300 ${
                        f.accesos > 0
                          ? 'bg-gradient-to-t from-[#EA580C] to-orange-400 shadow-sm'
                          : 'bg-slate-200'
                      }`}
                    />
                  </div>
                  <span className="text-[11px] font-semibold text-slate-500">{f.franja}</span>
                </div>
              )
            })}
          </div>
        </section>

        <section className="tarjeta flex flex-col justify-between space-y-4 lg:col-span-4">
          <div>
            <h2 className="text-lg font-bold text-zinc-900">Accesos Directos</h2>
            <p className="text-xs text-slate-500">Operaciones frecuentes de recepción</p>

            <div className="mt-4 space-y-3">
              <AccesoDirecto
                a="/acceso"
                titulo="Validar Check-In en Torniquete"
                subtitulo="Probar acceso con documento de socio"
                Icono={ScanLine}
              />
              <AccesoDirecto
                a="/socios"
                titulo="Socios y Planes"
                subtitulo="Registrar socio o asignar membresía con descuento"
                Icono={Users}
              />
              <AccesoDirecto
                a="/congelamientos"
                titulo="Congelar o Reactivar Plan"
                subtitulo="Extensión automática de fecha de vencimiento"
                Icono={Snowflake}
              />
              <AccesoDirecto
                a="/ajustes"
                titulo="Simular Hora / Ajustes"
                subtitulo="Probar bloqueo fuera de horario en vivo"
                Icono={Settings}
              />
            </div>
          </div>
        </section>
      </div>
    </div>
  )
}

function TarjetaKpi({ titulo, valor, detalle, Icono, acento }) {
  return (
    <article className="tarjeta flex items-start justify-between">
      <div>
        <p className="text-xs font-semibold uppercase tracking-wider text-slate-500">{titulo}</p>
        <p className="mt-2 text-3xl font-extrabold tracking-tight text-zinc-900">{valor}</p>
        <p className="mt-1 text-xs text-slate-500">{detalle}</p>
      </div>
      <div className={`grid h-12 w-12 shrink-0 place-items-center rounded-2xl ${acento}`}>
        <Icono className="h-6 w-6" />
      </div>
    </article>
  )
}

function AccesoDirecto({ a, titulo, subtitulo, Icono }) {
  return (
    <Link
      to={a}
      className="group flex items-center justify-between rounded-xl border border-[#E2E8F0] p-3.5 transition hover:border-[#EA580C]/50 hover:bg-orange-50/40"
    >
      <div className="flex items-center gap-3">
        <div className="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-zinc-700 transition group-hover:bg-[#EA580C] group-hover:text-white">
          <Icono className="h-5 w-5" />
        </div>
        <div>
          <p className="text-sm font-bold text-zinc-900">{titulo}</p>
          <p className="text-xs text-slate-500">{subtitulo}</p>
        </div>
      </div>
      <ArrowUpRight className="h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-[#EA580C]" />
    </Link>
  )
}
