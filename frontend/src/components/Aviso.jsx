import { AlertTriangle, CheckCircle2, X } from 'lucide-react'

export default function Aviso({ aviso, onCerrar }) {
  if (!aviso) return null
  const esExito = aviso.tipo === 'ok'

  return (
    <div
      role="alert"
      className={`animate-pop flex items-start justify-between gap-3 rounded-xl border px-4 py-3 text-sm font-medium shadow-sm ${
        esExito
          ? 'border-emerald-200 bg-emerald-50 text-emerald-900'
          : 'border-rose-200 bg-rose-50 text-rose-900'
      }`}
    >
      <div className="flex items-center gap-2.5">
        {esExito ? (
          <CheckCircle2 className="h-5 w-5 shrink-0 text-emerald-600" />
        ) : (
          <AlertTriangle className="h-5 w-5 shrink-0 text-rose-600" />
        )}
        <p>{aviso.texto}</p>
      </div>
      {onCerrar && (
        <button
          type="button"
          onClick={onCerrar}
          aria-label="Cerrar aviso"
          className="rounded-lg p-1 opacity-70 transition hover:bg-black/5 hover:opacity-100"
        >
          <X className="h-4 w-4" />
        </button>
      )}
    </div>
  )
}
