import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

// El proxy evita problemas de CORS en desarrollo: /api -> Laravel (php artisan serve)
export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      '/api': { target: 'http://127.0.0.1:8000', changeOrigin: true },
    },
  },
})
