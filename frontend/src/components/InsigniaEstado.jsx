import { CheckCircle2, Clock, Snowflake, XCircle } from 'lucide-react'

const ESTILOS = {
  ACTIVA: {
    clase: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    Icono: CheckCircle2,
  },
  VENCIDA: {
    clase: 'bg-rose-50 text-rose-700 ring-rose-600/20',
    Icono: XCircle,
  },
  CONGELADA: {
    clase: 'bg-sky-50 text-sky-700 ring-sky-600/20',
    Icono: Snowflake,
  },
}

export default function InsigniaEstado({ estado }) {
  const conf = ESTILOS[estado] ?? {
    clase: 'bg-slate-100 text-slate-600 ring-slate-500/20',
    Icono: Clock,
  }
  const { Icono } = conf

  return (
    <span
      className={`inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${conf.clase}`}
    >
      <Icono className="h-3.5 w-3.5" />
      {estado ?? 'SIN PLAN'}
    </span>
  )
}
