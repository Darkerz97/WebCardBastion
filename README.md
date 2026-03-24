# Card Bastion Server - Fase 1

Servidor central para Card Bastion construido con Laravel 12, MySQL/MariaDB y Laravel Sanctum.

## Objetivo

Esta fase entrega una base funcional para:

- autenticacion web y API
- productos
- clientes
- ventas, items y pagos
- dispositivos POS
- logs de sincronizacion
- dashboard administrativo con Blade
- API REST preparada para sincronizacion futura

La arquitectura evita dependencias de WebSockets, Redis o procesos persistentes para poder correr primero en hosting tradicional y migrar despues a VPS sin rehacer el backend.

## Stack

- PHP 8.2+
- Laravel 12
- MySQL o MariaDB
- Laravel Sanctum
- Blade para panel administrativo

## Estructura recomendada

```text
app/
  Http/
    Controllers/
      Api/
      Web/
    Middleware/
    Requests/
    Resources/
  Models/
  Services/
  Support/
database/
  migrations/
  seeders/
    Demo/
resources/
  views/
    auth/
    customers/
    dashboard/
    layouts/
    products/
    sales/
routes/
  api.php
  web.php
```

## Modulos incluidos

### Autenticacion

- login API con Sanctum
- logout API
- `/api/me`
- login web para panel Blade
- roles base: `admin`, `manager`, `cashier`
- seeder de admin inicial

### Catalogo

- CRUD completo de productos
- CRUD completo de clientes
- busqueda y filtros basicos
- soft deletes en productos y clientes
- importacion masiva por plantilla CSV compatible con Excel para productos y clientes

### Ventas

- ventas con items y pagos
- recalculo de subtotal, descuento y total
- actualizacion automatica de `payment_status`
- control de stock negativo bloqueado
- transacciones de base de datos para ventas y pagos
- importacion masiva por plantilla CSV compatible con Excel para ventas

### Sincronizacion

- `POST /api/sync/upload-sales`
- `GET /api/sync/products`
- `GET /api/sync/customers`
- `POST /api/sync/heartbeat`
- `sync_logs` para auditoria y trazabilidad

## Instalacion local

1. Instala dependencias:

```bash
composer install
```

2. Crea el archivo de entorno:

```bash
copy .env.example .env
```

3. Genera la llave:

```bash
php artisan key:generate
```

4. Configura MySQL o MariaDB en `.env`.

5. Ejecuta migraciones y seeders:

```bash
php artisan migrate --seed
```

6. Levanta el servidor:

```bash
php artisan serve
```

## Variables de entorno utiles

```env
APP_NAME="Card Bastion Server"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=card_bastion
DB_USERNAME=root
DB_PASSWORD=

ADMIN_NAME="Card Bastion Admin"
ADMIN_EMAIL=admin@cardbastion.test
ADMIN_PASSWORD=password
ADMIN_PHONE=5550000000
```

## Credenciales demo

Despues de `php artisan migrate --seed`:

- Admin web/API: `admin@cardbastion.test` / `password`
- Caja demo: `cashier@cardbastion.test` / `password`

## Rutas principales

### Web

- `/login`
- `/dashboard`
- `/products`
- `/customers`
- `/sales`
- `/products/template`
- `/products/import`
- `/customers/template`
- `/customers/import`
- `/sales/template`
- `/sales/import`

### API

- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/me`
- `GET /api/products`
- `GET /api/customers`
- `GET /api/sales`
- `POST /api/sales`
- `POST /api/sales/{sale}/payments`
- `GET /api/devices`
- `POST /api/sync/heartbeat`
- `GET /api/sync/products`
- `GET /api/sync/customers`
- `POST /api/sync/upload-sales`

## Arquitectura implementada

- **Controllers Web y API separados** para mantener clara la capa HTTP y facilitar crecimiento a POS, app movil y e-commerce.
- **Form Requests** para centralizar validacion.
- **API Resources** para respuestas JSON consistentes.
- **Services** para encapsular logica transaccional de ventas y sincronizacion.
- **Eloquent relationships** completas entre usuarios, roles, ventas, items, pagos, dispositivos y logs.
- **UUIDs unicos** en entidades clave para interoperabilidad futura con POS local y sincronizacion offline-first.
- **Blade admin simple** para operacion inmediata en hosting tradicional.
- **Importacion CSV reutilizable** con un lector comun para cargas masivas desde plantillas editables en Excel.

## Carga masiva desde Excel

Las pantallas de `Productos`, `Clientes` y `Ventas` incluyen dos acciones:

- `Descargar plantilla` para bajar un archivo `.csv`
- `Importar` para subir el archivo ya capturado

Aunque el formato es `.csv`, se puede abrir y editar directamente en Excel sin problema.

### Productos

La plantilla de productos usa estas columnas:

- `name`
- `sku`
- `barcode`
- `description`
- `category`
- `cost`
- `price`
- `stock`
- `image_path`
- `active`

Reglas importantes:

- `sku` identifica el producto para crear o actualizar
- `barcode` debe ser unico si se captura
- `active` acepta `1` o `0`

### Clientes

La plantilla de clientes usa estas columnas:

- `name`
- `phone`
- `email`
- `notes`
- `credit_balance`
- `active`

Reglas importantes:

- si coincide `email` o `phone`, el cliente se actualiza
- si no existe coincidencia, se crea un cliente nuevo
- `active` acepta `1` o `0`

### Ventas

La plantilla de ventas usa estas columnas:

- `sale_number`
- `customer_email`
- `customer_phone`
- `device_code`
- `sold_at`
- `status`
- `discount`
- `product_sku`
- `quantity`
- `unit_price`
- `payment_method`
- `payment_amount`
- `payment_reference`
- `payment_notes`
- `payment_paid_at`

Reglas importantes:

- repite el mismo `sale_number` en varias filas cuando una venta tenga varios productos
- `product_sku` debe existir previamente
- `customer_email` o `customer_phone` deben existir si se capturan
- `payment_method` y `payment_amount` deben capturarse juntos
- `status` acepta `draft`, `completed` o `cancelled`

## Despliegue

### Hosting tradicional

- apunta el dominio a `public/`
- configura `.env` con MySQL real
- ejecuta `php artisan migrate --force --seed`
- asegurate de que `storage/` y `bootstrap/cache/` tengan permisos de escritura

### VPS futuro

La app ya queda lista para migrar a VPS sin cambiar la arquitectura base. Solo sera necesario ajustar:

- variables de entorno
- permisos de sistema
- cache/queue si mas adelante se activan servicios adicionales
- proxy web y SSL

## Siguientes fases sugeridas

- sincronizacion bidireccional incremental
- control fino por permisos
- imagenes de productos
- reportes y exportaciones
- rewards y creditos
- e-commerce y preventas
- torneos y eventos
