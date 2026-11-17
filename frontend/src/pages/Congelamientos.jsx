import { useCallback, useEffect, useState } from 'react'
import { Calendar, Play, Snowflake } from 'lucide-react'
import { api } from '../api/cliente'
import InsigniaEstado from '../components/InsigniaEstado'
import Aviso from '../components/Aviso'

const DIAS_MINIMOS = 7
const DIAS_MAXIMOS = 30
const CHIPS_RAPIDOS = [7, 15, 30]

function sumarDiasIso(fechaIso, diasASumar) {
  if (!fechaIso) return '—'
  const [a, m, d] = fechaIso.split('-').map(Number)
  const fecha = new Date(Date.UTC(a, m - 1, d))
  fecha.setUTCDate(fecha.getUTCDate() + (Number(diasASumar) || 0))
  return fecha.toISOString().slice(0, 10)
}

export default function Congelamientos() {
  const [socios, setSocios] = useState([])
  const [diasPorMembresia, setDiasPorMembresia] = useState({})
  const [aviso, setAviso] = useState(null)
  const [procesando, setProcesando] = useState(null)

  const cargar = useCallback(async () => {
    try {
      setSocios(await api.listarSocios())
    } catch (error) {
      setAviso({ tipo: 'error', texto: error.message })
    }
  }, [])

  useEffect(() => {
    cargar()
  }, [cargar])

  async function congelar(membresia) {
    const dias = Number(diasPorMembresia[membresia.id] ?? 7)
    setProcesando(membresia.id)
    try {
      const dato = await api.congelarMembresia(membresia.id, dias)
      setAviso({
        tipo: 'ok',
        texto: `Membresía congelada por ${dato.diasCongelamiento} días. Al reactivarla se sumarán automáticamente.`,
      })
      await cargar()
    } catch (error) {
      setAviso({ tipo: 'error', texto: error.message })
    } finally {
      setProcesando(null)
    }
  }

  async function reactivar(membresia) {
    setProcesando(membresia.id)
    try {
      const dato = await api.reactivarMembresia(membresia.id)
      setAviso({
        tipo: 'ok',
        texto: `Membresía reactivada. Nueva fecha de vencimiento extendida: ${dato.fechaFin}.`,
      })
      await cargar()
    } catch (error) {
      setAviso({ tipo: 'error', texto: error.message })
    } finally {
      setProcesando(null)
    }
  }

  const gestionables = socios.filter((s) =>
    ['ACTIVA', 'CONGELADA'].includes(s.membresia?.estado),
  )

  return (
    <div className="space-y-8">
      <header>
        <h1 className="text-3xl font-extrabold tracking-tight text-zinc-900">
          Gestión de Congelamientos
        </h1>
        <p className="text-sm text-slate-600">
          Regla de dominio: solo se pueden congelar membresías ACTIVAS entre {DIAS_MINIMOS} y{' '}
          {DIAS_MAXIMOS} días. Al reactivar, la fecha de vencimiento se extiende automáticamente.
        </p>
      </header>

      <Aviso aviso={aviso} onCerrar={() => setAviso(null)} />

      <div className="grid gap-6 md:grid-cols-2">
        {gestionables.length === 0 && (
          <div className="tarjeta col-span-full text-center text-slate-500">
            No hay membresías activas ni congeladas en este momento.
          </div>
        )}

        {gestionables.map((s) => {
          const m = s.membresia
          const congelada = m.estado === 'CONGELADA'
          const diasElegidos = Number(diasPorMembresia[m.id] ?? 7)
          const nuevaFechaEstimada = sumarDiasIso(
            m.fechaFin,
            congelada ? m.diasCongelamiento : diasElegidos,
          )

          return (
            <article
              key={m.id}
              id={`membresia-${m.id}`}
              className="tarjeta flex flex-col justify-between space-y-5"
            >
              <div className="flex items-start justify-between gap-3">
                <div>
                  <h2 className="text-lg font-extrabold text-zinc-900">{s.nombre}</h2>
                  <p className="text-xs text-slate-500">
                    Doc. {s.documento} · Plan {m.plan} ({m.tipoTarifa})
                  </p>
                </div>
                <InsigniaEstado estado={m.estado} />
              </div>

              <div className="grid grid-cols-2 gap-3 rounded-xl border border-[#E2E8F0] bg-slate-50 p-3.5 text-xs">
                <div>
                  <p className="text-slate-500">Vencimiento actual</p>
                  <p className="mt-0.5 font-mono text-sm font-bold text-zinc-900">{m.fechaFin}</p>
                </div>
                <div>
                  <p className="text-slate-500">Nuevo vencimiento tras reactivar</p>
                  <p className="mt-0.5 flex items-center gap-1 font-mono text-sm font-bold text-[#EA580C]">
                    <Calendar className="h-3.5 w-3.5" />
                    {nuevaFechaEstimada}
                  </p>
                </div>
              </div>

              {congelada ? (
                <div className="space-y-3">
                  <div className="rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2.5 text-xs font-medium text-sky-900">
                    Congelada desde <strong>{m.fechaCongelamiento}</strong> por{' '}
                    <strong>{m.diasCongelamiento} días</strong>.
                  </div>
                  <button
                    type="button"
                    disabled={procesando === m.id}
                    onClick={() => reactivar(m)}
                    className="boton w-full"
                  >
                    <Play className="h-4 w-4" />
                    {procesando === m.id
                      ? 'Reactivando…'
                      : `Reactivar ahora (+${m.diasCongelamiento} días)`}
                  </button>
                </div>
              ) : (
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <span className="etiqueta mb-0">Días a congelar (7 a 30)</span>
                    <div className="flex gap-1.5">
                      {CHIPS_RAPIDOS.map((c) => (
                        <button
                          key={c}
                          type="button"
                          onClick={() => setDiasPorMembresia({ ...diasPorMembresia, [m.id]: c })}
                          className={`rounded-lg px-2.5 py-1 text-xs font-bold transition ${
                            diasElegidos === c
                              ? 'bg-[#EA580C] text-white'
                              : 'bg-slate-100 text-zinc-700 hover:bg-slate-200'
                          }`}
                        >
                          {c}d
                        </button>
                      ))}
                    </div>
                  </div>

                  <div className="flex items-center gap-3">
                    <input
                      id={`dias-${m.id}`}
                      type="number"
                      min={DIAS_MINIMOS}
                      max={DIAS_MAXIMOS}
                      value={diasElegidos}
                      onChange={(e) =>
                        setDiasPorMembresia({ ...diasPorMembresia, [m.id]: e.target.value })
                      }
                      className="campo w-28 font-bold"
                    />
                    <button
                      type="button"
                      disabled={procesando === m.id}
                      onClick={() => congelar(m)}
                      className="boton-oscuro flex-1"
                    >
                      <Snowflake className="h-4 w-4 text-sky-400" />
                      {procesando === m.id ? 'Congelando…' : 'Congelar Membresía'}
                    </button>
                  </div>
                </div>
              )}
            </article>
          )
        })}
      </div>
    </div>
  )
}
