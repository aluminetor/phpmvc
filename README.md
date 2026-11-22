# Ejercicios PHP

Una aplicación Laravel 13 que reúne los once ejercicios MVC. Los módulos con datos persistentes usan MySQL.

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

@(
    'Tarea', 'Gasto', 'Servicio', 'Reserva', 'Nota', 'Evento', 'Receta',
    'RecetaGuardada', 'PartidaMemoria', 'PuntajeMemoria', 'Encuesta', 'OpcionEncuesta',
    'VotoEncuesta', 'Cronometro', 'Vuelta'
) | ForEach-Object { php artisan make:model $_ -m }
```

`make:model -m` genera el modelo Eloquent y la migración juntos. Las clases `CalculoPropina` y `GeneradorContrasenas` contienen la lógica de sus respectivos módulos y no requieren tablas.

## Uso de los módulos

- `GET /gastos/resumen?mes=2026-11` muestra el total mensual y los subtotales por categoría. El formulario acepta punto o coma decimal.
- `GET /reservas/disponibilidad` recibe `servicio_id` y `fecha` en formato `AAAA-MM-DD`. El catálogo inicial incluye Asesoría, Atención técnica y Consulta, con citas de 30 minutos de lunes a viernes, de 09:00 a 17:00 en hora de Bogotá.
- `GET /notas?q=idea&categoria=trabajo` busca la palabra clave en el título y el contenido, y aplica el filtro de categoría.
- `GET /eventos?mes=2026-11` abre el calendario mensual en hora de Bogotá. Los recordatorios se muestran en el calendario al cargar la página dentro del intervalo elegido.
- `GET /recetas?tipo=almuerzo&ingrediente=arroz` combina ambos filtros. Usa `guardadas=1` para consultar los favoritos compartidos en la instalación local. Cada receta publicada tiene su propio enlace.
- `GET /memoria?dificultad=facil` muestra los diez mejores puntajes del nivel, ordenados por intentos y tiempo. Los niveles fácil, medio y difícil tienen 4, 6 y 8 pares.
- `/encuestas` permite publicar una pregunta con 2 a 8 opciones y recibe un voto anónimo por navegador. Los resultados muestran los votos y porcentajes por opción.
- `/cronometro` conserva el tiempo y las vueltas por navegador. La pausa congela el tiempo acumulado, y reiniciar pone el contador a cero y borra sus vueltas.
