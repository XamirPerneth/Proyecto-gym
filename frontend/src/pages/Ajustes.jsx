import { useCallback, useEffect, useState } from 'react'
import { Clock, Percent, RotateCcw, Save, Settings as IconoAjustes } from 'lucide-react'
import { api, formatearPesos } from '../api/cliente'
import { useAuth } from '../auth/AuthContext'
import Aviso from '../components/Aviso'

export default function Ajustes() {
  const { setAjustesClub } = useAuth()
  const [nombreGimnasio, setNombreGimnasio] = useState('Gym Control Club')
  const [descuentoTrimestral, setDescuentoTrimestral] = useState(10)
  const [descuentoAnual, setDescuentoAnual] = useState(20)
  const [usarHoraSimulada, setUsarHoraSimulada] = useState(false)
  const [horaSimulada, setHoraSimulada] = useState('2026-11-17T18:30')
  const [planes, setPlanes] = useState([])
  const [aviso, setAviso] = useState(null)
  const [guardando, setGuardando] = useState(false)

  const cargar = useCallback(async () => {
    try {
      const datos = await api.obtenerAjustes()
      setNombreGimnasio(datos.nombreGimnasio)
      setDescuentoTrimestral(datos.descuentoTrimestral)
      setDescuentoAnual(datos.descuentoAnual)
      setUsarHoraSimulada(Boolean(datos.horaSimulada))
      if (datos.horaSimulada) {
        setHoraSimulada(datos.horaSimulada.replace(' ', 'T'))
      }
      setPlanes(datos.planes ?? [])
    } catch (err) {
      setAviso({ tipo: 'error', texto: err.message })
    }
  }, [])

  useEffect(() => {
    cargar()
  }, [cargar])

  function cambiarPlan(id, campo, valor) {
    setPlanes((prev) => prev.map((p) => (p.id === id ? { ...p, [campo]: valor } : p)))
  }

  async function guardar(e) {
    e.preventDefault()
    setGuardando(true)
    setAviso(null)
    try {
      const actualizado = await api.actualizarAjustes({
        nombre_gimnasio: nombreGimnasio,
        descuento_trimestral: Number(descuentoTrimestral),
        descuento_anual: Number(descuentoAnual),
        hora_simulada: usarHoraSimulada ? horaSimulada.replace('T', ' ') : null,
        planes: planes.map((p) => ({
          id: p.id,
          hora_inicio: p.horaInicio,
          hora_fin: p.horaFin,
          precio_base_mensual: Number(p.precioBaseMensual),
        })),
      })

      setAjustesClub({
        nombreGimnasio: actualizado.nombreGimnasio,
        horaSimulada: actualizado.horaSimulada,
      })
      setAviso({
        tipo: 'ok',
        texto: 'Ajustes del gimnasio guardados y aplicados inmediatamente en el Dominio.',
      })
    } catch (err) {
      setAviso({ tipo: 'error', texto: err.message })
    } finally {
      setGuardando(false)
    }
  }

  function fijarHoraPruebaNocturna() {
    setUsarHoraSimulada(true)
    setHoraSimulada('2026-11-17T18:30')
  }

  return (
    <div className="space-y-8">
      <header>
        <h1 className="text-3xl font-extrabold tracking-tight text-zinc-900">
          Ajustes del Gimnasio
        </h1>
        <p className="text-sm text-slate-600">
          Configura horarios de los planes, precios base, descuentos (OCP) y el simulador de reloj
          para pruebas de recepción.
        </p>
      </header>

      <Aviso aviso={aviso} onCerrar={() => setAviso(null)} />

      <form onSubmit={guardar} className="space-y-6">
        <div className="grid gap-6 lg:grid-cols-2">
          {/* Identidad y Descuentos */}
          <section className="tarjeta space-y-4">
            <h2 className="flex items-center gap-2 text-lg font-bold text-zinc-900">
              <IconoAjustes className="h-5 w-5 text-[#EA580C]" />
              Identidad y Estrategias de Descuento
            </h2>

            <div>
              <label htmlFor="nombre-gimnasio" className="etiqueta">
                Nombre comercial del gimnasio
              </label>
              <input
                id="nombre-gimnasio"
                required
                value={nombreGimnasio}
                onChange={(e) => setNombreGimnasio(e.target.value)}
                className="campo"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label htmlFor="desc-trimestral" className="etiqueta">
                  Descuento Trimestral (%)
                </label>
                <div className="relative">
                  <input
                    id="desc-trimestral"
                    type="number"
                    min={0}
                    max={100}
                    value={descuentoTrimestral}
                    onChange={(e) => setDescuentoTrimestral(e.target.value)}
                    className="campo pr-9 font-bold"
                  />
                  <Percent className="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                </div>
              </div>

              <div>
                <label htmlFor="desc-anual" className="etiqueta">
                  Descuento Anual (%)
                </label>
                <div className="relative">
                  <input
                    id="desc-anual"
                    type="number"
                    min={0}
                    max={100}
                    value={descuentoAnual}
                    onChange={(e) => setDescuentoAnual(e.target.value)}
                    className="campo pr-9 font-bold"
                  />
                  <Percent className="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                </div>
              </div>
            </div>
          </section>

          {/* Simulador de Hora para el Torniquete */}
          <section className="tarjeta space-y-4 border-orange-200 bg-orange-50/30">
            <div className="flex items-start justify-between gap-3">
              <div>
                <h2 className="flex items-center gap-2 text-lg font-bold text-zinc-900">
                  <Clock className="h-5 w-5 text-[#EA580C]" />
                  Hora Simulada de Prueba (RelojPort)
                </h2>
                <p className="text-xs text-slate-600">
                  Permite demostrar en vivo cómo el torniquete bloquea el Plan Básico fuera de su
                  rango (06:00 a 16:00).
                </p>
              </div>
              <label className="inline-flex cursor-pointer items-center gap-2 text-xs font-bold">
                <input
                  id="chk-hora-simulada"
                  type="checkbox"
                  checked={usarHoraSimulada}
                  onChange={(e) => setUsarHoraSimulada(e.target.checked)}
                  className="h-4 w-4 accent-[#EA580C]"
                />
                Activar
              </label>
            </div>

            <div>
              <label htmlFor="input-hora-simulada" className="etiqueta">
                Fecha y hora del simulador
              </label>
              <input
                id="input-hora-simulada"
                type="datetime-local"
                disabled={!usarHoraSimulada}
                value={horaSimulada}
                onChange={(e) => setHoraSimulada(e.target.value)}
                className="campo font-mono disabled:opacity-50"
              />
            </div>

            <div className="flex flex-wrap gap-2">
              <button
                type="button"
                onClick={fijarHoraPruebaNocturna}
                className="boton-suave text-xs"
              >
                Probar 18:30 (Bloquea Plan Básico)
              </button>
              {usarHoraSimulada && (
                <button
                  type="button"
                  onClick={() => setUsarHoraSimulada(false)}
                  className="boton-suave text-xs"
                >
                  <RotateCcw className="h-3.5 w-3.5" />
                  Volver a hora real del servidor
                </button>
              )}
            </div>
          </section>
        </div>

        {/* Configuración de Planes */}
        <section className="tarjeta space-y-4">
          <h2 className="text-lg font-bold text-zinc-900">
            Horarios de Apertura/Cierre y Precios Base por Plan
          </h2>

          <div className="grid gap-4 md:grid-cols-2">
            {planes.map((plan) => (
              <div
                key={plan.id}
                className="space-y-3 rounded-2xl border border-[#E2E8F0] bg-slate-50/70 p-4"
              >
                <div className="flex items-center justify-between">
                  <h3 className="text-base font-extrabold text-zinc-900">Plan {plan.nombre}</h3>
                  <span className="text-xs font-semibold text-[#EA580C]">
                    Base actual: {formatearPesos(plan.precioBaseMensual)} / mes
                  </span>
                </div>

                <div className="grid grid-cols-3 gap-3">
                  <div>
                    <label className="etiqueta">Apertura</label>
                    <input
                      type="time"
                      value={plan.horaInicio}
                      onChange={(e) => cambiarPlan(plan.id, 'horaInicio', e.target.value)}
                      className="campo font-mono"
                    />
                  </div>
                  <div>
                    <label className="etiqueta">Cierre</label>
                    <input
                      type="time"
                      value={plan.horaFin}
                      onChange={(e) => cambiarPlan(plan.id, 'horaFin', e.target.value)}
                      className="campo font-mono"
                    />
                  </div>
                  <div>
                    <label className="etiqueta">Precio Mensual (COP)</label>
                    <input
                      type="number"
                      min={0}
                      step={1000}
                      value={plan.precioBaseMensual}
                      onChange={(e) => cambiarPlan(plan.id, 'precioBaseMensual', e.target.value)}
                      className="campo font-bold"
                    />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </section>

        <div className="flex justify-end">
          <button id="btn-guardar-ajustes" type="submit" disabled={guardando} className="boton px-8 py-3">
            <Save className="h-4 w-4" />
            {guardando ? 'Guardando cambios…' : 'Guardar configuración del gimnasio'}
          </button>
        </div>
      </form>
    </div>
  )
}
