import { useCallback, useEffect, useState } from 'react'
import {
  Clock,
  CreditCard,
  Plus,
  Search,
  Sparkles,
  UserPlus,
  X,
} from 'lucide-react'
import { api, formatearPesos } from '../api/cliente'
import InsigniaEstado from '../components/InsigniaEstado'
import Aviso from '../components/Aviso'

export default function SociosMembresias() {
  const [socios, setSocios] = useState([])
  const [planes, setPlanes] = useState([])
  const [busqueda, setBusqueda] = useState('')
  const [aviso, setAviso] = useState(null)

  const [modalSocio, setModalSocio] = useState(false)
  const [nuevoSocio, setNuevoSocio] = useState({ documento: '', nombre: '' })

  const [modalAsignar, setModalAsignar] = useState(false)
  const [formAsignar, setFormAsignar] = useState({
    socioId: '',
    planId: '',
    tipoTarifa: 'MENSUAL',
  })
  const [errorModal, setErrorModal] = useState('')
  const [enviando, setEnviando] = useState(false)

  const cargar = useCallback(async () => {
    try {
      const [listaSocios, listaPlanes] = await Promise.all([api.listarSocios(), api.listarPlanes()])
      setSocios(listaSocios)
      setPlanes(listaPlanes)
      setFormAsignar((f) => ({ ...f, planId: f.planId || String(listaPlanes[0]?.id ?? '') }))
    } catch (error) {
      setAviso({ tipo: 'error', texto: error.message })
    }
  }, [])

  useEffect(() => {
    cargar()
  }, [cargar])

  const sociosFiltrados = socios.filter((s) => {
    const q = busqueda.trim().toLowerCase()
    if (!q) return true
    return s.nombre.toLowerCase().includes(q) || s.documento.toLowerCase().includes(q)
  })

  function abrirModalAsignarPara(socioId = '') {
    setErrorModal('')
    setFormAsignar((f) => ({
      ...f,
      socioId: socioId ? String(socioId) : f.socioId,
      planId: f.planId || String(planes[0]?.id ?? ''),
    }))
    setModalAsignar(true)
  }

  async function guardarNuevoSocio(e) {
    e.preventDefault()
    setErrorModal('')
    setEnviando(true)
    try {
      const creado = await api.registrarSocio(nuevoSocio)
      setModalSocio(false)
      setNuevoSocio({ documento: '', nombre: '' })
      setAviso({ tipo: 'ok', texto: `Socio ${creado.nombre} registrado correctamente.` })
      await cargar()
    } catch (err) {
      setErrorModal(err.message)
    } finally {
      setEnviando(false)
    }
  }

  async function guardarAsignacion(e) {
    e.preventDefault()
    setErrorModal('')
    setEnviando(true)
    try {
      const m = await api.asignarMembresia({
        socio_id: Number(formAsignar.socioId),
        plan_id: Number(formAsignar.planId),
        tipo_tarifa: formAsignar.tipoTarifa,
      })
      setModalAsignar(false)
      setAviso({
        tipo: 'ok',
        texto: `Membresía ${m.plan} (${m.tipoTarifa}) asignada. Vence el ${m.fechaFin}.`,
      })
      await cargar()
    } catch (err) {
      setErrorModal(err.message)
    } finally {
      setEnviando(false)
    }
  }

  const planSeleccionado = planes.find((p) => String(p.id) === String(formAsignar.planId))
  const tarifaMensual = planSeleccionado?.tarifas.find((t) => t.tipo === 'MENSUAL')
  const tarifaSeleccionada = planSeleccionado?.tarifas.find((t) => t.tipo === formAsignar.tipoTarifa)
  const mesesSegunTarifa =
    formAsignar.tipoTarifa === 'ANUAL' ? 12 : formAsignar.tipoTarifa === 'TRIMESTRAL' ? 3 : 1
  const subtotalSinDescuento = (tarifaMensual?.precio ?? 0) * mesesSegunTarifa
  const ahorroPesos = Math.max(0, subtotalSinDescuento - (tarifaSeleccionada?.precio ?? 0))
  const porcentajeReal =
    subtotalSinDescuento > 0 ? Math.round((ahorroPesos / subtotalSinDescuento) * 100) : 0

  return (
    <div className="space-y-8">
      <header className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-extrabold tracking-tight text-zinc-900">
            Socios y Membresías
          </h1>
          <p className="text-sm text-slate-600">
            Administra el padrón de socios y asigna planes con cálculo automático de tarifas (OCP).
          </p>
        </div>

        <div className="flex flex-wrap gap-3">
          <button
            id="btn-abrir-nuevo-socio"
            type="button"
            onClick={() => {
              setErrorModal('')
              setModalSocio(true)
            }}
            className="boton-oscuro"
          >
            <UserPlus className="h-4 w-4 text-[#EA580C]" />
            Registrar nuevo socio
          </button>
          <button
            id="btn-abrir-asignar"
            type="button"
            onClick={() => abrirModalAsignarPara('')}
            className="boton"
          >
            <CreditCard className="h-4 w-4" />
            Asignar membresía
          </button>
        </div>
      </header>

      <Aviso aviso={aviso} onCerrar={() => setAviso(null)} />

      {/* Tarjetas visuales de los planes con imágenes locales */}
      <div className="grid gap-6 md:grid-cols-2">
        {planes.map((plan) => {
          const esFull = plan.nombre.toLowerCase().includes('full')
          const img = esFull ? '/images/plan-full.jpg' : '/images/plan-basico.jpg'
          const mensual = plan.tarifas.find((t) => t.tipo === 'MENSUAL')

          return (
            <article
              key={plan.id}
              className="overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm"
            >
              <div className="relative h-44 bg-[#18181B]">
                <img
                  src={img}
                  alt={`Plan ${plan.nombre}`}
                  onError={(e) => {
                    e.currentTarget.style.display = 'none'
                  }}
                  className="h-full w-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#18181B] via-[#18181B]/45 to-transparent" />
                <div className="absolute bottom-4 left-5 right-5 flex items-end justify-between text-white">
                  <div>
                    <span className="inline-flex items-center gap-1 rounded-full bg-[#EA580C] px-2.5 py-0.5 text-[11px] font-bold uppercase">
                      <Clock className="h-3 w-3" /> {plan.horaInicio} a {plan.horaFin}
                    </span>
                    <h2 className="mt-1 text-2xl font-extrabold">Plan {plan.nombre}</h2>
                  </div>
                  {mensual && (
                    <div className="text-right">
                      <p className="text-xs text-zinc-300">Desde / mes</p>
                      <p className="text-xl font-extrabold">{formatearPesos(mensual.precio)}</p>
                    </div>
                  )}
                </div>
              </div>

              <div className="grid grid-cols-3 divide-x divide-[#E2E8F0] bg-slate-50/60 p-4 text-center">
                {plan.tarifas.map((t) => (
                  <div key={t.tipo} className="px-2">
                    <p className="text-[11px] font-bold uppercase text-slate-500">{t.tipo}</p>
                    <p className="mt-0.5 text-sm font-extrabold text-zinc-900">
                      {formatearPesos(t.precio)}
                    </p>
                    <p className="text-[11px] text-slate-500">{t.duracionDias} días</p>
                  </div>
                ))}
              </div>
            </article>
          )
        })}
      </div>

      {/* Buscador y Tabla de socios */}
      <div className="tarjeta space-y-4">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div className="relative w-full max-w-md">
            <Search className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input
              id="buscar-socio"
              type="text"
              value={busqueda}
              onChange={(e) => setBusqueda(e.target.value)}
              placeholder="Buscar socio por nombre o documento…"
              className="campo pl-10"
            />
          </div>
          <span className="text-xs font-semibold text-slate-500">
            Mostrando {sociosFiltrados.length} de {socios.length} socios
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm" id="tabla-socios">
            <thead className="border-b border-[#E2E8F0] text-xs font-semibold uppercase tracking-wider text-slate-500">
              <tr>
                <th className="pb-3 pr-4">Socio</th>
                <th className="pb-3 pr-4">Plan y Tarifa</th>
                <th className="pb-3 pr-4">Horario</th>
                <th className="pb-3 pr-4">Estado</th>
                <th className="pb-3 pr-4">Vigencia</th>
                <th className="pb-3 pr-4">Valor Pagado</th>
                <th className="pb-3 text-right">Acción</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E2E8F0]">
              {sociosFiltrados.map((s) => {
                const m = s.membresia
                const puedeRenovar = !m || m.estado === 'VENCIDA'
                return (
                  <tr key={s.id} className="hover:bg-slate-50/80">
                    <td className="py-3.5 pr-4">
                      <p className="font-bold text-zinc-900">{s.nombre}</p>
                      <p className="font-mono text-xs text-slate-500">Doc. {s.documento}</p>
                    </td>
                    <td className="py-3.5 pr-4">
                      {m ? (
                        <div>
                          <p className="font-semibold text-zinc-900">Plan {m.plan}</p>
                          <p className="text-xs text-slate-500">{m.tipoTarifa}</p>
                        </div>
                      ) : (
                        <span className="text-slate-400">—</span>
                      )}
                    </td>
                    <td className="py-3.5 pr-4 text-xs font-medium text-slate-600">
                      {m?.horarioPermitido ?? '—'}
                    </td>
                    <td className="py-3.5 pr-4">
                      <InsigniaEstado estado={m?.estado} />
                    </td>
                    <td className="py-3.5 pr-4 text-xs text-slate-600">
                      {m ? `${m.fechaInicio} → ${m.fechaFin}` : '—'}
                    </td>
                    <td className="py-3.5 pr-4 font-semibold text-zinc-800">
                      {m ? formatearPesos(m.precioFinal) : '—'}
                    </td>
                    <td className="py-3.5 text-right">
                      <button
                        type="button"
                        onClick={() => abrirModalAsignarPara(s.id)}
                        className="boton-suave"
                      >
                        <Plus className="h-3.5 w-3.5 text-[#EA580C]" />
                        {puedeRenovar ? 'Asignar plan' : 'Nueva membresía'}
                      </button>
                    </td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal: Registrar nuevo socio */}
      {modalSocio && (
        <div className="fixed inset-0 z-50 grid place-items-center bg-black/50 p-4 backdrop-blur-xs">
          <div className="animate-pop w-full max-w-md rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-2xl">
            <div className="flex items-center justify-between">
              <h2 className="text-lg font-extrabold text-zinc-900">Registrar Nuevo Socio</h2>
              <button
                type="button"
                onClick={() => setModalSocio(false)}
                className="rounded-lg p-1 text-slate-400 hover:bg-slate-100"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            {errorModal && (
              <p className="mt-3 rounded-xl bg-rose-50 p-3 text-xs font-semibold text-rose-700">
                {errorModal}
              </p>
            )}

            <form onSubmit={guardarNuevoSocio} className="mt-4 space-y-4">
              <div>
                <label htmlFor="nuevo-documento" className="etiqueta">
                  Documento de identidad
                </label>
                <input
                  id="nuevo-documento"
                  required
                  value={nuevoSocio.documento}
                  onChange={(e) => setNuevoSocio({ ...nuevoSocio, documento: e.target.value })}
                  placeholder="Ej: 1004004004"
                  className="campo"
                />
              </div>

              <div>
                <label htmlFor="nuevo-nombre" className="etiqueta">
                  Nombre completo
                </label>
                <input
                  id="nuevo-nombre"
                  required
                  value={nuevoSocio.nombre}
                  onChange={(e) => setNuevoSocio({ ...nuevoSocio, nombre: e.target.value })}
                  placeholder="Ej: Sofía Hernández"
                  className="campo"
                />
              </div>

              <div className="flex justify-end gap-2 pt-2">
                <button
                  type="button"
                  onClick={() => setModalSocio(false)}
                  className="boton-suave px-4 py-2.5"
                >
                  Cancelar
                </button>
                <button id="btn-guardar-socio" type="submit" disabled={enviando} className="boton">
                  {enviando ? 'Guardando…' : 'Registrar socio'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal: Asignar membresía con desglose de descuento */}
      {modalAsignar && (
        <div className="fixed inset-0 z-50 grid place-items-center bg-black/50 p-4 backdrop-blur-xs">
          <div className="animate-pop w-full max-w-lg rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-2xl">
            <div className="flex items-center justify-between">
              <h2 className="text-lg font-extrabold text-zinc-900">Asignar Membresía</h2>
              <button
                type="button"
                onClick={() => setModalAsignar(false)}
                className="rounded-lg p-1 text-slate-400 hover:bg-slate-100"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            {errorModal && (
              <p className="mt-3 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-700">
                {errorModal}
              </p>
            )}

            <form onSubmit={guardarAsignacion} className="mt-4 space-y-4" id="form-asignar-membresia">
              <div>
                <label htmlFor="socio" className="etiqueta">
                  Socio
                </label>
                <select
                  id="socio"
                  required
                  value={formAsignar.socioId}
                  onChange={(e) => setFormAsignar({ ...formAsignar, socioId: e.target.value })}
                  className="campo"
                >
                  <option value="">Selecciona un socio…</option>
                  {socios.map((s) => (
                    <option key={s.id} value={s.id}>
                      {s.nombre} ({s.documento}) — {s.membresia?.estado ?? 'SIN PLAN'}
                    </option>
                  ))}
                </select>
              </div>

              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <label htmlFor="plan" className="etiqueta">
                    Plan
                  </label>
                  <select
                    id="plan"
                    value={formAsignar.planId}
                    onChange={(e) => setFormAsignar({ ...formAsignar, planId: e.target.value })}
                    className="campo"
                  >
                    {planes.map((p) => (
                      <option key={p.id} value={p.id}>
                        Plan {p.nombre} ({p.horaInicio}–{p.horaFin})
                      </option>
                    ))}
                  </select>
                </div>

                <div>
                  <label htmlFor="tarifa" className="etiqueta">
                    Modalidad de Tarifa
                  </label>
                  <select
                    id="tarifa"
                    value={formAsignar.tipoTarifa}
                    onChange={(e) => setFormAsignar({ ...formAsignar, tipoTarifa: e.target.value })}
                    className="campo"
                  >
                    {(planSeleccionado?.tarifas ?? []).map((t) => (
                      <option key={t.tipo} value={t.tipo}>
                        {t.tipo} ({t.duracionDias} días)
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              {tarifaSeleccionada && (
                <div className="space-y-2 rounded-2xl border border-orange-200 bg-orange-50/60 p-4 text-xs">
                  <div className="flex justify-between text-slate-600">
                    <span>
                      Subtotal ({mesesSegunTarifa}{' '}
                      {mesesSegunTarifa === 1 ? 'mes' : 'meses'} ×{' '}
                      {formatearPesos(tarifaMensual?.precio ?? 0)})
                    </span>
                    <span className="font-semibold">{formatearPesos(subtotalSinDescuento)}</span>
                  </div>
                  <div className="flex justify-between text-emerald-700">
                    <span className="flex items-center gap-1 font-semibold">
                      <Sparkles className="h-3.5 w-3.5" />
                      Descuento estrategia {formAsignar.tipoTarifa} ({porcentajeReal} %)
                    </span>
                    <span className="font-bold">− {formatearPesos(ahorroPesos)}</span>
                  </div>
                  <div className="flex items-center justify-between border-t border-orange-200 pt-2 text-sm">
                    <span className="font-bold text-zinc-900">
                      Total a pagar ({tarifaSeleccionada.duracionDias} días)
                    </span>
                    <span className="text-lg font-extrabold text-[#EA580C]">
                      {formatearPesos(tarifaSeleccionada.precio)}
                    </span>
                  </div>
                </div>
              )}

              <div className="flex justify-end gap-2 pt-2">
                <button
                  type="button"
                  onClick={() => setModalAsignar(false)}
                  className="boton-suave px-4 py-2.5"
                >
                  Cancelar
                </button>
                <button
                  id="btn-asignar"
                  type="submit"
                  disabled={enviando || !formAsignar.socioId}
                  className="boton"
                >
                  {enviando ? 'Asignando…' : 'Confirmar y activar plan'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  )
}
