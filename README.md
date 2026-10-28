# Prueba Técnica API

API REST de Categorías y Productos hecha con Laravel 11.

## Metodología de desarrollo

El andamiaje del proyecto (estructura de carpetas, configuración de Swagger, Telescope y Activity Log) se generó con asistencia de IA, como apoyo para agilizar tareas repetitivas de configuración. La lógica de negocio —validaciones, reglas de dominio (por ejemplo, la restricción para eliminar categorías con productos asociados) y los controllers— fue implementada de forma manual. Los tests se desarrollaron también con asistencia de IA.

## Requisitos previos

- PHP 8.2 o superior
- Composer
- SQLite (viene con PHP, no requiere instalación aparte) o MySQL si prefieres usarlo

## Instalación

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --port=8000
```

Con eso el servidor queda levantado en `http://localhost:8000`.

El seeder crea 4 categorías y 5 productos por categoría, listos para probar.

## URLs base

| Recurso                  | URL                                        |
| ------------------------ | ------------------------------------------ |
| API                      | `http://localhost:8000/api`                |
| Swagger / documentación  | `http://localhost:8000/api/documentation`  |
| Telescope                | `http://localhost:8000/telescope`          |

## Endpoints

| Método | Ruta                    | Descripción                                                      |
| ------ | ----------------------- | ------------------------------------------------------------------ |
| GET    | `/api/categories`       | Listar categorías                                                 |
| POST   | `/api/categories`       | Crear categoría                                                    |
| GET    | `/api/categories/{id}`  | Ver una categoría                                                  |
| PUT    | `/api/categories/{id}`  | Actualizar categoría                                               |
| DELETE | `/api/categories/{id}`  | Eliminar categoría (falla con 409 si tiene productos asociados)   |
| GET    | `/api/products`        | Listar productos                                                  |
| POST   | `/api/products`        | Crear producto                                                     |
| GET    | `/api/products/{id}`   | Ver un producto                                                    |
| PUT    | `/api/products/{id}`   | Actualizar producto                                                |
| DELETE | `/api/products/{id}`   | Eliminar producto                                                  |

## Estructura del proyecto

```text
app/
├─ Http/
│  ├─ Controllers/Api/      # CategoryController, ProductController
│  ├─ Requests/             # Validación de cada endpoint (FormRequest)
│  └─ Resources/            # Formato de respuesta JSON
├─ Models/                  # Category, Product (con auditoría via ActivityLog)
└─ Traits/ApiResponse.php   # Helpers success()/error() para respuestas estándar

database/
├─ migrations/
├─ factories/
└─ seeders/

routes/api.php
```

## Manejo de errores

Todas las respuestas siguen el mismo formato:

```json
{
  "success": true,
  "message": "Producto creado correctamente",
  "data": { "id": 1, "name": "Camiseta Overskull", "price": 89.90 }
}
```

```json
{
  "success": false,
  "message": "Los datos enviados no son válidos.",
  "errors": { "name": ["El campo 'name' es obligatorio."] }
}
```

- Errores de validación → `422`
- Recurso no encontrado → `404`
- Eliminar una categoría con productos asociados → `409`
- El manejo está centralizado en `bootstrap/app.php` (no hay un try/catch repetido en cada controller).

## Auditoría y observabilidad

- Cada `create`/`update`/`delete` sobre `Category` y `Product` queda registrado en el log de auditoría (`spatie/laravel-activitylog`).
- Telescope está habilitado solo en `local`/`staging` (se registra condicionalmente en `AppServiceProvider`); en producción queda deshabilitado.
