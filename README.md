# Gym Hexagonal

Gestión de membresías y control de acceso de un gimnasio (Arquitectura Hexagonal).

- backend/: Laravel 10 API REST (app/Gym: Domain, Application, Infrastructure)
- frontend/: React + Vite + Tailwind CSS

## Puesta en marcha

1. Iniciar MySQL (XAMPP) y crear la BD gym_hexagonal.
2. cd backend; composer install; copiar .env.example a .env; php artisan key:generate; php artisan migrate:fresh --seed; php artisan serve
3. cd frontend; npm install; npm run dev (http://localhost:5173)

Pruebas: php vendor/bin/phpunit

