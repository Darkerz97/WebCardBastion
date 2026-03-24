# Card Bastion Platform

Plataforma Laravel 12 para `tienda virtual`, `portal de jugadores` y `panel administrativo`, compatible con `Hostinger compartido` usando Blade, Tailwind, Vite, MySQL/MariaDB y Sanctum.

## Estado actual

### Fase 1 implementada

- autenticacion web
- registro de jugadores
- roles: `admin`, `manager`, `cashier`, `player`
- layout publico, auth y admin con Blade + Tailwind + Vite
- categorias
- productos con slug, publicacion en tienda y galeria de imagenes
- catalogo publico
- detalle publico de producto
- panel admin de categorias
- panel admin de productos
- API basica de categorias y productos con Sanctum
- base previa reutilizada para clientes, ventas, dashboard y sincronizacion

### Siguientes fases pendientes

- Fase 2: carrito, checkout, pedidos, pagos, historial de compras
- Fase 3: perfiles de jugador completos, torneos, resultados, estadisticas
- Fase 4: creditos, recompensas, dashboard avanzado y API extendida

## Stack

- PHP 8.2+
- Laravel 12
- MySQL o MariaDB
- Laravel Sanctum
- Blade
- Tailwind CSS 4
- Vite

## Modulos activos

### Publico

- `/` y `/tienda`
- filtros por categoria
- detalle `/tienda/{slug}`
- login y registro de jugadores
- portal base del jugador en `/mi-cuenta`

### Admin

- `/dashboard`
- `/categories`
- `/products`
- `/customers`
- `/sales`

### API

- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `GET /api/products`
- `GET /api/categories`
- endpoints previos de clientes, ventas, dispositivos y sync

## Base de datos agregada en Fase 1

- `categories`
- `product_images`
- nuevas columnas en `products`:
  - `category_id`
  - `slug`
  - `short_description`
  - `featured`
  - `publish_to_store`

## Credenciales demo

Despues de sembrar la base:

- Admin: `admin@cardbastion.test` / `password`
- Manager: `manager@cardbastion.test` / `password`
- Cashier: `cashier@cardbastion.test` / `password`
- Player: `player@cardbastion.test` / `password`

## Instalacion local

1. Instala dependencias PHP:

```bash
composer install
```

2. Instala dependencias frontend:

```bash
npm install
```

3. Crea `.env`:

```bash
copy .env.example .env
```

4. Genera la app key:

```bash
php artisan key:generate
```

5. Configura MySQL/MariaDB en `.env`.

6. Ejecuta migraciones y seeders:

```bash
php artisan migrate --seed
```

7. Crea el enlace para archivos publicos:

```bash
php artisan storage:link
```

8. Levanta Vite en desarrollo:

```bash
npm run dev
```

9. Levanta Laravel:

```bash
php artisan serve
```

## Build para produccion

```bash
npm run build
php artisan view:cache
php artisan config:cache
php artisan route:cache
```

## Despliegue en Hostinger compartido

La implementacion esta pensada para hosting compartido, sin Redis, websockets ni supervisor.

### Recomendaciones

- apunta el dominio o subdominio a `public/`
- usa MySQL/MariaDB de Hostinger
- sube `vendor/` y `node_modules` no es necesario en produccion si ya subes `public/build`
- asegúrate de tener `storage/` y `bootstrap/cache/` con permisos de escritura
- ejecuta `php artisan storage:link` si el entorno lo permite
- si no puedes usar symlink, sirve imagenes desde rutas publicas o ajusta el hosting

### Flujo sugerido

1. En local:

```bash
composer install
npm install
npm run build
php artisan migrate --seed
```

2. Sube el proyecto al servidor con `public/build` incluido.

3. Configura `.env` del servidor.

4. Ejecuta en servidor:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Archivos clave de Fase 1

### Backend

- `app/Models/Category.php`
- `app/Models/Product.php`
- `app/Models/ProductImage.php`
- `app/Http/Controllers/Web/StorefrontController.php`
- `app/Http/Controllers/Web/CategoryController.php`
- `app/Http/Controllers/Web/ProductController.php`
- `app/Http/Controllers/Web/AuthController.php`
- `app/Http/Controllers/Web/AccountController.php`
- `app/Http/Controllers/Api/CategoryController.php`

### Frontend Blade

- `resources/views/layouts/public.blade.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/store/index.blade.php`
- `resources/views/store/show.blade.php`
- `resources/views/categories/*`
- `resources/views/products/*`

### Infraestructura

- `resources/css/app.css`
- `routes/web.php`
- `routes/api.php`
- `database/migrations/2026_03_24_020000_create_categories_table.php`
- `database/migrations/2026_03_24_020100_update_products_table_for_storefront.php`
- `database/migrations/2026_03_24_020200_create_product_images_table.php`

## Validaciones realizadas en esta fase

- `php artisan route:list`
- `php -l` sobre controladores y modelos nuevos
- `npm run build`
- `php artisan view:cache`

## Notas

- La importacion masiva CSV de productos existente sigue disponible y ahora soporta campos de tienda.
- Clientes y ventas del sistema previo se conservaron para no perder operacion ya construida.
- El carrito, checkout, pedidos, torneos y recompensas aun no estan implementados en esta fase.
