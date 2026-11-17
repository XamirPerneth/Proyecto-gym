import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Activity,
  AlertCircle,
  ArrowRight,
  Clock,
  Dumbbell,
  Eye,
  EyeOff,
  Lock,
  Mail,
  ShieldCheck,
  Sparkles,
  Star,
  Users,
} from 'lucide-react'
import { useAuth } from '../auth/AuthContext'

export default function Login() {
  const { iniciarSesion } = useAuth()
  const navigate = useNavigate()

  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [mostrarClave, setMostrarClave] = useState(false)
  const [error, setError] = useState('')
  const [cargando, setCargando] = useState(false)
  const [imagenOculta, setImagenOculta] = useState(false)

  async function autenticar(correo, clave) {
    setError('')
    setCargando(true)
    try {
      await iniciarSesion(correo, clave)
      navigate('/', { replace: true })
    } catch (err) {
      setError(err.message ?? 'Credenciales inválidas.')
    } finally {
      setCargando(false)
    }
  }

  function manejarSubmit(e) {
    e.preventDefault()
    autenticar(email, password)
  }

  function entrarDemo() {
    const correoDemo = 'admin@gymcontrol.com'
    const claveDemo = 'admin123'
    setEmail(correoDemo)
    setPassword(claveDemo)
    autenticar(correoDemo, claveDemo)
  }

  return (
    <div className="grid min-h-screen bg-[#F8FAFC] lg:grid-cols-12">
      {/* Panel izquierdo: Formulario de recepción / administrador */}
      <div className="flex flex-col justify-between px-6 py-10 sm:px-12 lg:col-span-5 xl:px-16">
        <div className="flex items-center gap-3">
          <span className="grid h-11 w-11 place-items-center rounded-xl bg-[#EA580C] text-white shadow-md shadow-[#EA580C]/25">
            <Dumbbell className="h-6 w-6" />
          </span>
          <div>
            <span className="text-lg font-extrabold tracking-tight text-zinc-900">
              GYM CONTROL <span className="text-[#EA580C]">PRO</span>
            </span>
            <p className="text-xs text-slate-500">Plataforma de Recepción y Control de Acceso</p>
          </div>
        </div>

        <div className="mx-auto my-10 w-full max-w-md space-y-7">
          <div>
            <span className="inline-flex items-center gap-1.5 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-[#EA580C] ring-1 ring-[#EA580C]/20">
              <ShieldCheck className="h-3.5 w-3.5" /> Portal de Personal Autorizado
            </span>
            <h1 className="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900 sm:text-4xl">
              Bienvenido al Club
            </h1>
            <p className="mt-2 text-sm text-slate-600">
              Ingresa con tu cuenta de recepción o administración para operar el torniquete, gestionar
              membresías y controlar congelamientos.
            </p>
          </div>

          {error && (
            <div
              role="alert"
              id="alerta-error-login"
              className="animate-pop flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm font-medium text-rose-800"
            >
              <AlertCircle className="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
              <span>{error}</span>
            </div>
          )}

          <form onSubmit={manejarSubmit} className="space-y-4">
            <div>
              <label htmlFor="login-email" className="etiqueta">
                Correo electrónico
              </label>
              <div className="relative">
                <Mail className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input
                  id="login-email"
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="admin@gymcontrol.com"
                  className="campo pl-10"
                />
              </div>
            </div>

            <div>
              <label htmlFor="login-password" className="etiqueta">
                Contraseña
              </label>
              <div className="relative">
                <Lock className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input
                  id="login-password"
                  type={mostrarClave ? 'text' : 'password'}
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••"
                  className="campo pl-10 pr-10"
                />
                <button
                  type="button"
                  onClick={() => setMostrarClave(!mostrarClave)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-zinc-700"
                  aria-label="Mostrar u ocultar contraseña"
                >
                  {mostrarClave ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </div>

            <button
              id="btn-ingresar"
              type="submit"
              disabled={cargando}
              className="boton w-full py-3 text-base"
            >
              {cargando ? 'Verificando acceso…' : 'Iniciar sesión en recepción'}
              <ArrowRight className="h-4 w-4" />
            </button>
          </form>

          <div className="relative">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-[#E2E8F0]" />
            </div>
            <div className="relative flex justify-center text-xs uppercase">
              <span className="bg-[#F8FAFC] px-3 font-semibold text-slate-400">
                Acceso rápido de demostración
              </span>
            </div>
          </div>

          <button
            id="btn-login-demo"
            type="button"
            disabled={cargando}
            onClick={entrarDemo}
            className="boton-oscuro w-full py-3"
          >
            <Sparkles className="h-4 w-4 text-[#EA580C]" />
            Entrar como Admin Demo (admin@gymcontrol.com)
          </button>
        </div>

        <p className="text-xs text-slate-400">
          Arquitectura Hexagonal · Puertos y Adaptadores · Dominio 100 % desacoplado
        </p>
      </div>

      {/* Panel derecho: Fotografía de gimnasio + degradado oscuro + tarjeta flotante */}
      <div className="relative hidden overflow-hidden bg-[#18181B] lg:col-span-7 lg:block">
        {!imagenOculta && (
          <img
            src="/images/login-fondo.jpg"
            alt="Entrenamiento de fuerza en el gimnasio"
            onError={() => setImagenOculta(true)}
            className="absolute inset-0 h-full w-full object-cover object-center"
          />
        )}
        <div className="absolute inset-0 bg-gradient-to-tr from-[#18181B] via-[#18181B]/75 to-[#EA580C]/25" />

        <div className="relative z-10 flex h-full flex-col justify-between p-12 text-white xl:p-16">
          <div className="flex items-center justify-end">
            <span className="inline-flex items-center gap-2 rounded-full bg-black/50 px-4 py-1.5 text-xs font-semibold text-orange-300 ring-1 ring-white/15 backdrop-blur-md">
              <Activity className="h-4 w-4 text-[#EA580C]" /> Torniquete biométrico y control horario en vivo
            </span>
          </div>

          <div className="space-y-8">
            <blockquote className="max-w-xl space-y-3">
              <p className="text-xs font-bold uppercase tracking-widest text-[#EA580C]">
                Filosofía de alto rendimiento
              </p>
              <p className="text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">
                “La disciplina en la entrada construye la constancia dentro del gimnasio.”
              </p>
              <p className="text-sm text-zinc-300">
                Controla vigencias, franjas horarias por plan y congelamientos médicos en milisegundos
                desde una sola consola de recepción.
              </p>
            </blockquote>

            {/* Tarjeta flotante con estadísticas del club */}
            <div className="grid max-w-2xl grid-cols-3 gap-4 rounded-2xl border border-white/15 bg-zinc-900/75 p-5 shadow-2xl backdrop-blur-md">
              <div className="flex items-center gap-3">
                <div className="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#EA580C]/20 text-[#EA580C]">
                  <Users className="h-5 w-5" />
                </div>
                <div>
                  <p className="text-xl font-extrabold text-white">+1.250</p>
                  <p className="text-xs text-zinc-400">Socios activos</p>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <div className="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-emerald-500/20 text-emerald-400">
                  <Star className="h-5 w-5" />
                </div>
                <div>
                  <p className="text-xl font-extrabold text-white">4.9 / 5.0</p>
                  <p className="text-xs text-zinc-400">Satisfacción recepción</p>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <div className="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-sky-500/20 text-sky-400">
                  <Clock className="h-5 w-5" />
                </div>
                <div>
                  <p className="text-xl font-extrabold text-white">05:00 – 23:00</p>
                  <p className="text-xs text-zinc-400">Horario continuo Full</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
