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

### Fase 2 implementada

- carrito de compras por sesion
- checkout autenticado
- creacion de pedidos reutilizando `sales`
- registro inicial de pagos en checkout
- historial de compras en portal de jugador
- rediseño premium de la home publica de la tienda con hero comercial, beneficios, filtros refinados y mejor jerarquia visual

### Siguientes fases pendientes

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
- `/carrito`
- `/checkout`
- login y registro de jugadores
- portal base del jugador en `/mi-cuenta`
- historial de compras en `/mi-cuenta/compras`

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

## Base de datos agregada

- `categories`
- `product_images`
- nueva relacion `customers.user_id`
- nuevos campos ecommerce en `sales`:
  - `order_channel`
  - `contact_name`
  - `contact_email`
  - `contact_phone`
  - `notes`
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
- `app/Http/Controllers/Web/CartController.php`
- `app/Http/Controllers/Web/CheckoutController.php`
- `app/Http/Controllers/Web/AccountOrderController.php`
- `app/Http/Controllers/Api/CategoryController.php`
- `app/Services/CartService.php`

### Frontend Blade

- `resources/views/layouts/public.blade.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/store/index.blade.php`
- `resources/views/store/show.blade.php`
- `resources/views/store/cart.blade.php`
- `resources/views/store/checkout.blade.php`
- `resources/views/account/orders.blade.php`
- `resources/views/categories/*`
- `resources/views/products/*`

### Infraestructura

- `resources/css/app.css`
- `routes/web.php`
- `routes/api.php`
- `database/migrations/2026_03_24_020000_create_categories_table.php`
- `database/migrations/2026_03_24_020100_update_products_table_for_storefront.php`
- `database/migrations/2026_03_24_020200_create_product_images_table.php`
- `database/migrations/2026_03_24_030000_update_customers_and_sales_for_store_orders.php`

## Validaciones realizadas en esta fase

- `php artisan route:list`
- `php -l` sobre controladores y modelos nuevos
- `npm run build`
- `php artisan view:cache`

## Notas

- La importacion masiva CSV de productos existente sigue disponible y ahora soporta campos de tienda.
- Clientes y ventas del sistema previo se conservaron para no perder operacion ya construida.
- Torneos, recompensas, creditos avanzados y el dashboard ampliado siguen pendientes para las siguientes fases.

## Correcciones recientes

- Se agrego un acceso directo desde `login` hacia `registro` para que los jugadores nuevos puedan crear su cuenta mas facilmente.
- La plantilla de autenticacion ahora muestra mensajes visibles de validacion y exito en `login` y `registro`.
- Para los cambios de torneos y portal de jugador, es necesario ejecutar `php artisan migrate` para crear tablas como `tournament_registrations` y evitar errores SQL por tablas faltantes.

## Rediseño reciente de storefront

Se rehizo la vista principal del ecommerce para que Card Bastion se perciba como una tienda real, premium y especializada, manteniendo Blade, Tailwind y la logica funcional existente.

### Mejoras visuales aplicadas

- header publico mas elegante, con mejor separacion visual, carrito mas visible y CTA principal de registro
- hero principal de dos columnas con copy comercial en español y mejor aprovechamiento del espacio
- composicion de destacados para evitar vacios visuales en desktop
- nueva seccion de beneficios enfocada en catalogo curado, comunidad y compra simple
- bloque de catalogo con encabezado mas fuerte, metricas visuales y mejor jerarquia
- sidebar de filtros refinado con inputs, focus states y CTA de limpiar filtros
- tarjetas de producto con mejor presencia visual, precio mas protagonista y estados mas comerciales
- empty state mas elegante para diferenciar entre catalogo sin coincidencias y catalogo en preparacion
- sistema visual mas consistente en colores, bordes, radios, sombras y superficies reutilizables

### Archivos tocados en el rediseño

- `resources/views/store/index.blade.php`
- `resources/views/layouts/public.blade.php`
- `resources/css/app.css`

### Validacion del cambio

- `npm run build`
- `php artisan view:cache`
