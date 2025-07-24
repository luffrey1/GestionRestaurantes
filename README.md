# 🚀 Despliegue y uso rápido

## Despliegue local (desarrollo) — **Usa docker-compose**

Para desarrollo local, utiliza **docker-compose.yml**. Esto levanta automáticamente:
- PHP + Symfony
- PostgreSQL 16
- Nginx

### Pasos rápidos:
1. Clona el repositorio y sitúate en la raíz del proyecto.
2. Ejecuta:
   ```sh
   docker-compose up --build
   ```
3. Accede a la app en [http://localhost](http://localhost)
4. **Ejecuta migraciones para crear la base de datos y los datos de ejemplo:**
   ```sh
   docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   ```
5. (Opcional) Accede a la base de datos:
   ```sh
   docker-compose exec postgres psql -U restaurantes_lo16_user -d restaurantes_lo16
   ```

---

## Despliegue en producción (Render) — **Usa el Dockerfile**

- El archivo `Dockerfile` está preparado para Render.
- Render orquesta la base de datos y el proxy, solo necesitas el Dockerfile y configurar las variables de entorno (`DATABASE_URL`, `API_KEY`, etc.) en el panel de Render.
- Asegúrate de añadir `server_version=16.0.0` en la URL de la base de datos o en la configuración de Doctrine.

---

# Prueba Técnica — API de Gestión de Restaurantes

## Descripción

Solución a la prueba técnica: API RESTful para gestionar restaurantes (nombre, dirección, teléfono) con operaciones CRUD, desarrollada en Symfony (PHP) y MySQL. Incluye extras opcionales como autenticación por API Key, Docker, documentación automática y frontend.

## ¿Qué incluye?
- CRUD de restaurantes
- Validación de datos y gestión de errores
- **Bonus:**
  - Autenticación por API Key (`X-API-KEY`)
  - Docker (Dockerfile y docker-compose)
  - Documentación automática (Swagger)
  - Frontend opcional (HTML + TailwindCSS + JS)

## Estructura
```
/backend      # Symfony API
/frontend     # Frontend opcional
/docker-compose.yml
/.env.example
/README.md
```

## Cómo ejecutar (Docker)
1. Clona el repositorio y copia el archivo de entorno:
   ```bash
   cp .env.example .env
   ```
2. Lanza los servicios:
   ```bash
   docker-compose up --build
   ```
3. Inicializa la base de datos:
   ```bash
   docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
   ```
4. Accede a la API:
   - **Restaurantes:** `http://localhost/api/restaurants`
   - **Healthcheck:** `http://localhost/api/health`
   - **Swagger:** `http://localhost/api/doc`
   - **Frontend:** `http://localhost` (opcional)

## Autenticación
Incluye el header en cada petición:
```
X-API-KEY: TU_API_KEY
```
(Ver `.env.example` para la clave)

## Endpoints principales
| Método | Endpoint                  | Descripción                  | Autenticación |
|--------|---------------------------|------------------------------|---------------|
| GET    | /api/restaurants          | Listar restaurantes          | Sí            |
| POST   | /api/restaurants          | Crear restaurante            | Sí            |
| GET    | /api/restaurants/{id}     | Obtener restaurante por ID   | Sí            |
| PUT    | /api/restaurants/{id}     | Actualizar restaurante       | Sí            |
| DELETE | /api/restaurants/{id}     | Eliminar restaurante         | Sí            |
| GET    | /api/health               | Healthcheck (público)        | No            |

## Ejemplo de uso
```bash
curl -H "X-API-KEY: TU_API_KEY" http://localhost/api/restaurants
```

## Notas
- API documentada automáticamente en `/api/doc` (Swagger)
- Frontend opcional y responsive
- Código solo para evaluación, sin derechos de uso por terceros

---

# Technical Test — Restaurant Management API

## Description

Solution for the technical test: RESTful API to manage restaurants (name, address, phone) with CRUD operations, built in Symfony (PHP) and MySQL. Includes optional extras: API Key authentication, Docker, automatic documentation, and optional frontend.

## Features
- Restaurant CRUD
- Data validation and error handling
- **Bonus:**
  - API Key authentication (`X-API-KEY`)
  - Docker (Dockerfile and docker-compose)
  - Automatic documentation (Swagger)
  - Optional frontend (HTML + TailwindCSS + JS)

## Structure
```
/backend      # Symfony API
/frontend     # Optional frontend
/docker-compose.yml
/.env.example
/README.md
```

## How to run (Docker)
1. Clone the repo and copy the env file:
   ```bash
   cp .env.example .env
   ```
2. Start the services:
   ```bash
   docker-compose up --build
   ```
3. Initialize the database:
   ```bash
   docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
   ```
4. Access the API:
   - **Restaurants:** `http://localhost/api/restaurants`
   - **Healthcheck:** `http://localhost/api/health`
   - **Swagger:** `http://localhost/api/doc`
   - **Frontend:** `http://localhost` (optional)

## Authentication
Include the header in every request:
```
X-API-KEY: YOUR_API_KEY
```
(See `.env.example` for the key)

## Main endpoints
| Method | Endpoint                  | Description                  | Authentication |
|--------|---------------------------|------------------------------|----------------|
| GET    | /api/restaurants          | List restaurants             | Yes            |
| POST   | /api/restaurants          | Create restaurant            | Yes            |
| GET    | /api/restaurants/{id}     | Get restaurant by ID         | Yes            |
| PUT    | /api/restaurants/{id}     | Update restaurant            | Yes            |
| DELETE | /api/restaurants/{id}     | Delete restaurant            | Yes            |
| GET    | /api/health               | Healthcheck (public)         | No             |

## Example usage
```bash
curl -H "X-API-KEY: YOUR_API_KEY" http://localhost/api/restaurants
```

## Notes
- API automatically documented at `/api/doc` (Swagger)
- Optional, responsive frontend
- Code for evaluation only, no rights granted to third parties 