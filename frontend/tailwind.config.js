/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,jsx}'],
  theme: {
    extend: {
      fontFamily: { sans: ['Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
      colors: {
        brand: {
          50: '#fff7ed',
          100: '#ffedd5',
          500: '#f97316',
          600: '#ea580c',
          700: '#c2410c',
        },
        carbon: {
          800: '#27272a',
          900: '#18181b',
        },
      },
      keyframes: {
        pop: {
          '0%': { opacity: 0, transform: 'translateY(6px) scale(.98)' },
          '100%': { opacity: 1, transform: 'translateY(0) scale(1)' },
        },
      },
      animation: { pop: 'pop .22s ease-out' },
    },
  },
  plugins: [],
}
