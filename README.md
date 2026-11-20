# Ejercicios PHP

Una aplicación Laravel 13 con navegación para los once ejercicios MVC. Esta primera entrega incluye las tareas persistentes en MySQL, la calculadora de propinas y el generador de contraseñas. Los módulos 4 al 11 muestran una página de estado y sus demás endpoints devuelven HTTP 501 hasta que se implemente su lógica.

## Ejecutar en Windows con WampServer

1. Inicia MySQL en WampServer. Usa PHP 8.3 o superior con `pdo_mysql` habilitado tanto en la consola como en Apache.
2. Crea la base de datos desde phpMyAdmin o desde el cliente MySQL:

   ```sql
   CREATE DATABASE IF NOT EXISTS seguimiento_php
       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Abre PowerShell en `C:\wamp64\www\seguimiento-php` y ejecuta:

   ```powershell
   composer install
   if (-not (Test-Path .env)) { Copy-Item .env.example .env }
   ```

4. Confirma los valores `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` de `.env`. La plantilla apunta a `127.0.0.1:3306` y usa `root` con contraseña vacía como valor local inicial. Si `APP_KEY` está vacío, ejecuta `php artisan key:generate`. Luego ejecuta:

   ```powershell
   php artisan migrate
   php artisan route:list --except-vendor
   php artisan serve
   ```

5. Abre [la aplicación local](http://127.0.0.1:8000). Para servirla con Apache de WampServer, crea un VirtualHost cuyo `DocumentRoot` apunte a `C:/wamp64/www/seguimiento-php/public`, habilita `mod_rewrite`, permite `AllowOverride All` en ese directorio y ajusta `APP_URL` a la URL del VirtualHost. Los estilos de Bootstrap se cargan desde CDN.

## Comandos de creación desde una carpeta vacía

Composer crea el proyecto. Artisan genera los archivos base. Ejecuta este bloque solo en un proyecto nuevo y aplica después el código de esta entrega.

```powershell
composer create-project laravel/laravel seguimiento-php "^13.0" --no-interaction --no-scripts
Set-Location seguimiento-php
Copy-Item .env.example .env
php artisan key:generate

@(
    'TareaController',
    'PropinaController',
    'ContrasenaController',
    'GastoController',
    'ReservaController',
    'NotaController',
    'EventoController',
    'RecetaController',
    'MemoriaController',
    'EncuestaController',
    'CronometroController'
) | ForEach-Object { php artisan make:controller $_ }

php artisan make:model Tarea -m
```

`make:model -m` genera el modelo Eloquent y la migración juntos. Las clases `CalculoPropina` y `GeneradorContrasenas` contienen la lógica de sus respectivos módulos y no requieren tablas.

Los siguientes comandos generan bases para los modelos y migraciones previstos en la segunda etapa. Las tablas necesitan su esquema funcional antes de que se ejecuten sus migraciones.

```powershell
@(
    'Gasto', 'Servicio', 'Reserva', 'Nota', 'Evento', 'Receta',
    'RecetaGuardada', 'PuntajeMemoria', 'Encuesta', 'OpcionEncuesta',
    'VotoEncuesta', 'Cronometro', 'Vuelta'
) | ForEach-Object { php artisan make:model $_ -m }
```

## Rutas de filtros

- `GET /gastos/resumen?mes=2026-11` prevé el resumen mensual.
- `GET /notas?q=idea&categoria=trabajo` prevé la búsqueda y el filtro de notas.
- `GET /recetas?tipo=almuerzo&ingrediente=arroz` prevé el filtro de recetas.
