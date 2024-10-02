# Event Reservation API
Este proyecto es una API para la gestión de reservas de espacios para eventos, desarrollada utilizando Laravel. La API permite a los usuarios registrar, iniciar sesión, crear y gestionar reservas, así como a los administradores gestionar espacios.

# Requisitos del Sistema
Antes de comenzar, asegúrate de tener instalado lo siguiente:

- PHP >= 8.0
- Composer (para gestionar dependencias de PHP)
- MySQL o cualquier otro sistema de base de datos compatible con Laravel
- Node.js >= 14 (solo si planeas trabajar con el frontend)
- Git (opcional)

## Dirígete al directorio del proyecto:

cd event-reservation-backend

## Instalación de Dependencias

### Instala todas las dependencias de PHP utilizando Composer:

composer install

### Instala todas las dependencias de Node.js (solo si tienes un frontend asociado):

npm install

### Configuración del Entorno

Crea un archivo .env basado en el archivo .env.example:

cp .env.example .env

Abre el archivo .env y edita las siguientes variables para configurar la conexión a la base de datos:

- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=nombre_de_tu_base_de_datos
- DB_USERNAME=tu_usuario
- DB_PASSWORD=tu_contraseña

### Genera la clave de aplicación de Laravel:

php artisan key:generate

### Migraciones y Seeds

Ejecuta las migraciones para crear las tablas en la base de datos:

php artisan migrate

Si tienes seeds (semillas) configuradas para popular las tablas con datos iniciales, puedes ejecutarlas con:

php artisan db:seed

Esto llenará la base de datos con algunos datos iniciales, como roles de usuarios, espacios de ejemplo, etc.

## JWT (JSON Web Token) Configuración

Genera la clave secreta para JWT:

php artisan jwt:secret

Esto agregará una nueva clave en el archivo .env llamada JWT_SECRET.

## Ejecutar el Servidor

### Ejecuta el servidor de desarrollo de Laravel:

php artisan serve

El servidor estará disponible en http://localhost:8000.

## Documentación de la API

### Acceso a la Documentación Swagger

La documentación de la API ha sido generada utilizando Swagger. Puedes acceder a ella en la siguiente URL:

http://localhost:8000/api/documentation

### Aquí verás todos los endpoints disponibles, los parámetros requeridos y las respuestas que puedes esperar.

## Endpoints Principales

### Autenticación
- POST /api/register: Registro de un nuevo usuario.
- POST /api/login: Inicio de sesión.
- GET /api/me: Obtener información del usuario autenticado.
### Espacios
- GET /api/spaces: Obtener la lista de todos los espacios disponibles.
- GET /api/spaces/{id}: Obtener información sobre un espacio específico.
- POST /api/spaces (Solo para administradores): Crear un nuevo espacio.
- PUT /api/spaces/{id} (Solo para administradores): Actualizar un espacio existente.
- DELETE /api/spaces/{id} (Solo para administradores): Eliminar un espacio.
### Reservas
- GET /api/reservations: Obtener todas las reservas del usuario autenticado.
- POST /api/reservations: Crear una nueva reserva.
- GET /api/reservations/{id}: Obtener una reserva específica del usuario autenticado.
- PUT /api/reservations/{id}: Actualizar una reserva existente.
- DELETE /api/reservations/{id}: Eliminar una reserva existente.

## Ejecución de Tests

### Para ejecutar las pruebas unitarias, utiliza el siguiente comando:
php artisan test

### Consideraciones Adicionales
Tokens JWT: Todos los endpoints que requieren autenticación necesitan un token JWT. Este token se obtiene al hacer login y debe ser enviado en el encabezado Authorization de cada solicitud, precedido de la palabra "Bearer".

Middleware de Admin: Las rutas para la creación, actualización y eliminación de espacios están protegidas por un middleware que garantiza que solo los usuarios con el rol de admin puedan acceder.

Swagger: La documentación se puede regenerar si realizas cambios en los controladores usando:

php artisan l5-swagger:generate

Storage de Imágenes: Las imágenes subidas para los espacios se guardan en la carpeta storage/app/public/spaces. Asegúrate de correr php artisan storage:link para crear un enlace simbólico hacia el directorio público.

### Ejemplos de Uso

Crear un Nuevo Usuario
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
  }'

Iniciar Sesión
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'

Crear una Reserva
```bash
curl -X POST http://localhost:8000/api/reservations \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "space_id": 1,
    "event_name": "Reunión",
    "reservation_date": "2024-10-03",
    "start_time": "08:00:00",
    "end_time": "10:00:00"
  }'







