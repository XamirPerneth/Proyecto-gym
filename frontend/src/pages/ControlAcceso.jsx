import { useCallback, useEffect, useState } from 'react'
import {
  Calendar,
  CheckCircle2,
  Clock,
  History,
  RefreshCw,
  ScanLine,
  ShieldAlert,
  ShieldCheck,
  Zap,
} from 'lucide-react'
import { api } from '../api/cliente'

const PRUEBAS_RAPIDAS = [
  {
    documento: '1001001001',
    nombre: 'Carlos Ramírez',
    etiqueta: 'Plan Full · Activo',
    color: 'border-emerald-200 bg-emerald-50/70 text-emerald-800 hover:bg-emerald-100',
  },
  {
    documento: '1002002002',
    nombre: 'Laura Gómez',
    etiqueta: 'Plan Básico (06:00–16:00)',
    color: 'border-orange-200 bg-orange-50/70 text-orange-800 hover:bg-orange-100',
  },
  {
    documento: '1003003003',
    nombre: 'Pedro Martínez',
    etiqueta: 'Membresía Vencida',
    color: 'border-rose-200 bg-rose-50/70 text-rose-800 hover:bg-rose-100',
  },
]

export default function ControlAcceso() {
  const [documento, setDocumento] = useState('')
  const [resultado, setResultado] = useState(null)
  const [cargando, setCargando] = useState(false)
  const [historial, setHistorial] = useState([])

  const cargarHistorial = useCallback(async () => {
    try {
      setHistorial(await api.listarAccesos(10))
    } catch {
      // Silencioso en polling
    }
  }, [])

  useEffect(() => {
    cargarHistorial()
    const intervalo = setInterval(cargarHistorial, 5000)
    return () => clearInterval(intervalo)
  }, [cargarHistorial])

  async function ejecutarCheckIn(docAValidar) {
    const limpio = docAValidar.trim()
    if (!limpio) return
    setDocumento(limpio)
    setCargando(true)
    setResultado(null)
    try {
      const datos = await api.validarCheckIn(limpio)
      setResultado({ permitido: true, datos })
    } catch (error) {
      setResultado({ permitido: false, motivo: error.message, documento: limpio })
    } finally {
      setCargando(false)
      cargarHistorial()
    }
  }

  function manejarSubmit(e) {
    e.preventDefault()
    ejecutarCheckIn(documento)
  }

  return (
    <div className="space-y-8">
      <header className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-extrabold tracking-tight text-zinc-900">
            Control de Acceso · Torniquete
          </h1>
          <p className="text-sm text-slate-600">
            Valida la membresía y el rango horario permitido por el Dominio antes de liberar el paso.
          </p>
        </div>
      </header>

      {/* 2 Columnas: Lector izquierda + Tarjeta de resultado derecha */}
      <div className="grid gap-6 lg:grid-cols-12">
        {/* Columna izquierda: Lector + botones rápidos */}
        <div className="tarjeta space-y-6 lg:col-span-5">
          <div className="flex items-center gap-3">
            <div className="grid h-11 w-11 place-items-center rounded-xl bg-orange-50 text-[#EA580C]">
              <ScanLine className="h-6 w-6" />
            </div>
            <div>
              <h2 className="text-lg font-bold text-zinc-900">Terminal de Lectura</h2>
              <p className="text-xs text-slate-500">Digita la cédula o usa un botón de prueba rápida</p>
            </div>
          </div>

          <form onSubmit={manejarSubmit} className="space-y-4">
            <div>
              <label htmlFor="documento" className="etiqueta">
                Documento del socio
              </label>
              <input
                id="documento"
                type="text"
                value={documento}
                onChange={(e) => setDocumento(e.target.value)}
                placeholder="Ej: 1001001001"
                autoFocus
                required
                className="campo py-3.5 text-xl font-bold tracking-wider"
              />
            </div>

            <button
              id="btn-validar-entrada"
              type="submit"
              disabled={cargando || !documento.trim()}
              className="boton w-full py-3.5 text-base"
            >
              <ScanLine className="h-5 w-5" />
              {cargando ? 'Validando acceso en Dominio…' : 'Validar Entrada'}
            </button>
          </form>

          <div className="space-y-2.5 border-t border-[#E2E8F0] pt-5">
            <p className="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">
              <Zap className="h-3.5 w-3.5 text-[#EA580C]" />
              Pruebas rápidas de 1 clic (Seeder)
            </p>
            <div className="space-y-2">
              {PRUEBAS_RAPIDAS.map((p) => (
                <button
                  key={p.documento}
                  id={`btn-rapido-${p.documento}`}
                  type="button"
                  disabled={cargando}
                  onClick={() => ejecutarCheckIn(p.documento)}
                  className={`flex w-full items-center justify-between rounded-xl border px-3.5 py-2.5 text-left text-xs font-semibold transition ${p.color}`}
                >
                  <div>
                    <p className="font-bold">{p.nombre}</p>
                    <p className="opacity-80">Doc: {p.documento}</p>
                  </div>
                  <span className="rounded-lg bg-white/80 px-2.5 py-1 text-[11px] font-bold shadow-sm">
                    {p.etiqueta}
                  </span>
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Columna derecha: Tarjeta visual grande de resultado */}
        <div className="lg:col-span-7">
          <TarjetaResultadoTorniquete resultado={resultado} />
        </div>
      </div>

      {/* Tabla inferior: Historial en vivo de los últimos ingresos */}
      <section className="tarjeta space-y-4">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <History className="h-5 w-5 text-[#EA580C]" />
            <div>
              <h2 className="text-lg font-bold text-zinc-900">
                Historial en Vivo de Últimos Ingresos
              </h2>
              <p className="text-xs text-slate-500">
                Auditoría directa de la tabla <code className="font-mono">registros_acceso</code>
              </p>
            </div>
          </div>
          <button type="button" onClick={cargarHistorial} className="boton-suave">
            <RefreshCw className="h-3.5 w-3.5" />
            Actualizar
          </button>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm" id="tabla-historial-accesos">
            <thead className="border-b border-[#E2E8F0] text-xs font-semibold uppercase tracking-wider text-slate-500">
              <tr>
                <th className="pb-3 pr-4">Fecha y Hora</th>
                <th className="pb-3 pr-4">Socio</th>
                <th className="pb-3 pr-4">Plan</th>
                <th className="pb-3 pr-4">Resultado</th>
                <th className="pb-3">Detalle / Motivo del Dominio</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E2E8F0]">
              {historial.length === 0 && (
                <tr>
                  <td colSpan={5} className="py-8 text-center text-slate-400">
                    Aún no hay registros de acceso.
                  </td>
                </tr>
              )}
              {historial.map((item) => (
                <tr key={item.id} className="hover:bg-slate-50/80">
                  <td className="whitespace-nowrap py-3 pr-4 font-mono text-xs text-slate-600">
                    {item.fechaHora}
                  </td>
                  <td className="py-3 pr-4">
                    <p className="font-semibold text-zinc-900">{item.socio}</p>
                    <p className="text-xs text-slate-500">{item.documento}</p>
                  </td>
                  <td className="py-3 pr-4 font-medium text-zinc-700">{item.plan}</td>
                  <td className="py-3 pr-4">
                    {item.permitido ? (
                      <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/20">
                        <CheckCircle2 className="h-3.5 w-3.5" /> PERMITIDO
                      </span>
                    ) : (
                      <span className="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-600/20">
                        <ShieldAlert className="h-3.5 w-3.5" /> DENEGADO
                      </span>
                    )}
                  </td>
                  <td className="py-3 text-xs text-slate-600">
                    {item.motivo ?? 'Ingreso válido dentro de horario y vigencia.'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  )
}

function TarjetaResultadoTorniquete({ resultado }) {
  if (!resultado) {
    return (
      <div className="tarjeta flex h-full min-h-[340px] flex-col items-center justify-center text-center">
        <div className="grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-slate-400">
          <ScanLine className="h-8 w-8" />
        </div>
        <h3 className="mt-4 text-lg font-bold text-zinc-800">Torniquete en Espera</h3>
        <p className="mt-1 max-w-sm text-sm text-slate-500">
          Escanea o selecciona un documento a la izquierda para ver la respuesta inmediata del Dominio.
        </p>
      </div>
    )
  }

  if (resultado.permitido) {
    const d = resultado.datos
    const iniciales = d.socio
      .split(' ')
      .map((n) => n[0])
      .slice(0, 2)
      .join('')
      .toUpperCase()

    return (
      <div
        role="status"
        id="alerta-acceso-permitido"
        className="animate-pop flex h-full min-h-[340px] flex-col justify-between rounded-3xl bg-gradient-to-br from-emerald-600 to-emerald-800 p-8 text-white shadow-xl shadow-emerald-600/20"
      >
        <div className="flex items-center justify-between">
          <span className="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1 text-xs font-bold uppercase tracking-wider">
            <ShieldCheck className="h-4 w-4" /> Acceso Permitido · Torniquete Abierto
          </span>
          <span className="font-mono text-sm text-emerald-100">{d.horaIngreso}</span>
        </div>

        <div className="my-6 flex items-center gap-5">
          <div className="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-white text-2xl font-extrabold text-emerald-700 shadow-lg">
            {iniciales}
          </div>
          <div>
            <p className="text-sm font-medium text-emerald-100">Doc. {d.documento}</p>
            <h2 className="text-3xl font-extrabold tracking-tight sm:text-4xl">{d.socio}</h2>
            <p className="mt-1 text-sm font-semibold text-emerald-100">
              Plan {d.plan} · {d.diasRestantes ?? '—'} días restantes
            </p>
          </div>
        </div>

        <dl className="grid grid-cols-2 gap-3 sm:grid-cols-3">
          <div className="rounded-2xl bg-black/20 p-3.5">
            <dt className="flex items-center gap-1.5 text-xs text-emerald-100">
              <Clock className="h-3.5 w-3.5" /> Horario del Plan
            </dt>
            <dd className="mt-1 text-base font-bold">{d.horarioPermitido}</dd>
          </div>
          <div className="rounded-2xl bg-black/20 p-3.5">
            <dt className="flex items-center gap-1.5 text-xs text-emerald-100">
              <Calendar className="h-3.5 w-3.5" /> Vencimiento
            </dt>
            <dd className="mt-1 text-base font-bold">{d.fechaVencimiento}</dd>
          </div>
          <div className="col-span-2 rounded-2xl bg-black/20 p-3.5 sm:col-span-1">
            <dt className="text-xs text-emerald-100">Días restantes</dt>
            <dd className="mt-1 text-base font-bold">{d.diasRestantes ?? 0} días</dd>
          </div>
        </dl>
      </div>
    )
  }

  return (
    <div
      role="alert"
      id="alerta-acceso-denegado"
      className="animate-pop flex h-full min-h-[340px] flex-col justify-between rounded-3xl bg-gradient-to-br from-rose-600 to-rose-900 p-8 text-white shadow-xl shadow-rose-600/20"
    >
      <div className="flex items-center justify-between">
        <span className="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1 text-xs font-bold uppercase tracking-wider">
          <ShieldAlert className="h-4 w-4" /> Acceso Denegado · Torniquete Bloqueado
        </span>
        {resultado.documento && (
          <span className="font-mono text-xs text-rose-200">Doc. {resultado.documento}</span>
        )}
      </div>

      <div className="my-6 space-y-3">
        <p className="text-xs font-bold uppercase tracking-wider text-rose-200">
          Motivo devuelto por el Dominio:
        </p>
        <h2 className="text-2xl font-extrabold leading-snug sm:text-3xl">{resultado.motivo}</h2>
      </div>

      <p className="rounded-2xl bg-black/20 p-3.5 text-xs text-rose-100">
        Si la membresía está vencida o congelada, puedes renovarla o reactivarla desde las secciones de{' '}
        <strong>Socios y Membresías</strong> o <strong>Congelamientos</strong>.
      </p>
    </div>
  )
}
