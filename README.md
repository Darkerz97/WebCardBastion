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
- refresh visual global con tema oscuro, superficies charcoal/navy y acentos ambar inspirados en la referencia del panel POS
- refresh visual global con tema oscuro, superficies charcoal/navy y acentos ambar inspirados en la referencia del panel POS

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
- `POST /api/sync/upload-products` para altas y cambios bidireccionales de productos desde POS

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
  - `min_stock`
  - `product_type`
  - `game`
  - `card_name`
  - `set_name`
  - `set_code`
  - `collector_number`
  - `finish`
  - `language`
  - `card_condition`

## Ajuste reciente del panel admin segun base de datos

Se alineo el panel administrativo con campos y relaciones que ya existian en la base para reducir huecos entre datos persistidos y lo visible en backoffice.

### Ajuste aplicado

- productos ahora permite capturar y editar `min_stock`, `product_type`, `game`, `card_name`, `set_name`, `set_code`, `collector_number`, `finish`, `language` y `card_condition`
- listados y detalle de productos ahora muestran metadata TCG/POS y alertas de stock bajo usando `min_stock`
- el dashboard admin ya calcula alertas de inventario con `stock <= min_stock` en lugar de un umbral fijo
- clientes ahora pueden vincular una cuenta `user` existente del sistema
- detalle de cliente ahora muestra cuenta vinculada, rol y preventas recientes
- ventas manuales ahora permiten capturar `order_channel`, datos de contacto y `notes`
- listados y detalle de ventas ahora muestran canal, contacto y timestamps de sincronizacion cuando existen

### Archivos clave

- `app/Http/Controllers/Web/ProductController.php`
- `app/Http/Requests/Product/AdminProductRequest.php`
- `resources/views/products/_form.blade.php`
- `resources/views/products/index.blade.php`
- `resources/views/products/show.blade.php`
- `app/Http/Controllers/Web/DashboardController.php`
- `app/Http/Controllers/Web/CustomerController.php`
- `app/Http/Requests/Customer/CustomerRequest.php`
- `resources/views/customers/_form.blade.php`
- `resources/views/customers/index.blade.php`
- `resources/views/customers/show.blade.php`
- `app/Http/Requests/Sale/StoreSaleRequest.php`
- `resources/views/sales/create.blade.php`
- `resources/views/sales/index.blade.php`
- `resources/views/sales/show.blade.php`

### Validacion aplicada

- `php artisan test`
- `php artisan view:cache`

## Ajuste reciente de estabilidad visual en autenticacion

Se reforzo la experiencia visual de login, registro y recuperacion para evitar que la pantalla quede atenuada u oscurecida de forma intermitente al enviar formularios o regresar desde el historial del navegador.

### Ajuste aplicado

- limpieza adicional de estados visuales globales en `pageshow` y `DOMContentLoaded`
- restauracion forzada de `opacity`, `filter`, `pointer-events` y `overflow` cuando la pagina auth se reactiva
- manejo mas seguro del submit en formularios de autenticacion para desactivar solo el boton mientras procesa
- nuevo estado visual `Procesando...` sin oscurecer toda la vista

### Archivos clave

- `resources/js/app.js`
- `resources/css/app.css`
- `resources/views/layouts/auth.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`

### Validacion aplicada

- `npm run build`
- `php artisan view:cache`

## Modulo reciente de preventas en panel admin

Se construyo la capa web/admin de preventas para que el equipo pueda crear reservas, asignarlas a clientes y dar seguimiento de abonos sin depender solo de la API.

### Alcance del modulo

- listado admin de preventas con filtros por cliente, estatus y fechas
- alta manual de preventas con cliente, productos y abono inicial opcional
- vista detalle con cliente asociado, cuenta vinculada, items reservados, total, abonado y saldo pendiente
- registro de nuevos abonos desde el detalle
- actualizacion de estatus para seguimiento operativo (`pending`, `partially_paid`, `paid`, `delivered`, `cancelled`)
- acceso directo desde menu lateral y dashboard admin

### Rutas web agregadas

- `GET /preorders`
- `GET /preorders/create`
- `POST /preorders`
- `GET /preorders/{preorder}`
- `POST /preorders/{preorder}/payments`
- `PATCH /preorders/{preorder}/status`

### Archivos clave

- `app/Http/Controllers/Web/PreorderController.php`
- `app/Http/Requests/Preorder/UpdatePreorderStatusRequest.php`
- `routes/web.php`
- `resources/views/preorders/_form.blade.php`
- `resources/views/preorders/create.blade.php`
- `resources/views/preorders/index.blade.php`
- `resources/views/preorders/show.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/dashboard/index.blade.php`
- `app/Http/Controllers/Web/DashboardController.php`

### Validacion aplicada

- `php artisan route:list --name=preorders`
- `php artisan view:cache`
- `php artisan test`

## Modulo reciente de cierres de caja en panel admin

Se construyo la capa web/admin para consultar y registrar cierres de caja desde el panel, aprovechando la tabla `cash_closures` que ya existia por integracion POS.

### Alcance del modulo

- listado admin de cierres con filtros por dispositivo, usuario, estatus y rango de fechas
- alta manual de cierres con montos de apertura, ventas por metodo, esperado, cierre y diferencia
- vista detalle con dispositivo, usuario responsable, origen, timestamps de sync y notas
- actualizacion de estatus de conciliacion (`open`, `closed`, `reconciled`)
- acceso directo desde menu lateral y dashboard admin

### Rutas web agregadas

- `GET /cash-closures`
- `GET /cash-closures/create`
- `POST /cash-closures`
- `GET /cash-closures/{cashClosure}`
- `PATCH /cash-closures/{cashClosure}/status`

### Archivos clave

- `app/Http/Controllers/Web/CashClosureController.php`
- `app/Http/Requests/CashClosure/StoreCashClosureRequest.php`
- `app/Http/Requests/CashClosure/UpdateCashClosureStatusRequest.php`
- `routes/web.php`
- `resources/views/cash-closures/index.blade.php`
- `resources/views/cash-closures/create.blade.php`
- `resources/views/cash-closures/show.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/dashboard/index.blade.php`
- `app/Http/Controllers/Web/DashboardController.php`

### Validacion aplicada

- `php artisan route:list --name=cash-closures`
- `php artisan view:cache`
- `php artisan test`

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
- si no puedes usar symlink, la tienda ya incluye una ruta publica de respaldo para imagenes de producto, pero los archivos deben existir en `storage/app/public`

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

## Actualizacion reciente de paleta visual

Se ajusto la identidad visual base del proyecto para acercarla a una referencia de interfaz POS con tonos oscuros, dorados y alto contraste, sin cambiar la logica de negocio.

### Paleta aplicada

- fondo principal en navy oscuro y charcoal
- paneles y tarjetas con superficies oscuras reutilizables
- acentos ambar/dorado para CTA, logo, estados activos y metricas
- textos secundarios suavizados para mantener contraste sin perder legibilidad

### Alcance del cambio

- panel administrativo
- layout publico
- pantallas de autenticacion
- botones, inputs, tablas, tarjetas, badges y fondos globales

### Archivos clave

- `resources/css/app.css`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/public.blade.php`
- `resources/views/layouts/auth.blade.php`

### Validacion aplicada

- `npm run build`

## Actualizacion reciente de branding en header

Se integro la imagen de Card Bastion en el menu principal de la pagina publica para que el encabezado se acerque mas a la referencia visual del cliente.

### Ajustes aplicados

- logo usando `public/cardbastion-logo.png` dentro del bloque de marca principal
- contenedor del branding con capsula oscura, borde suave y sombra para separarlo del header
- mejor jerarquia visual entre icono, nombre `Card Bastion` y subtitulo comercial

### Archivo clave

- `public/cardbastion-logo.png`
- `resources/views/layouts/public.blade.php`

## Actualizacion reciente de recuperacion de contrasena

Se agrego el flujo web para recuperar y restablecer contrasena usando el broker nativo de Laravel y la tabla `password_reset_tokens` ya disponible en el proyecto.

### Flujo agregado

- enlace `Olvide mi contrasena` desde la pantalla de login
- formulario para solicitar enlace de recuperacion
- formulario para definir nueva contrasena a partir del token
- rutas web de recuperacion y restablecimiento integradas al modulo de autenticacion

### Cuenta admin agregada por migracion

- nombre: `jorge damian tenorio santacruz`
- email: `damian97santacruz@gmail.com`
- password inicial: `2802damiaN`

### Archivos clave

- `app/Http/Controllers/Web/PasswordResetController.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `routes/web.php`
- `database/migrations/2026_03_27_120000_create_damian_admin_account.php`

### Consideraciones

- es necesario ejecutar `php artisan migrate` para crear la cuenta admin fija
- el envio real del enlace de recuperacion requiere configurar correo en `.env`

## Ajuste reciente para produccion

Se eliminaron del login las referencias visibles a cuentas demo y credenciales sugeridas para que la experiencia publica no exponga datos de prueba.

### Ajuste aplicado

- se retiro el bloque de acceso demo del formulario de login
- se dejo un mensaje neutro orientado a recuperacion segura de contrasena

### Archivo clave

- `resources/views/auth/login.blade.php`

## Actualizacion reciente de vista de torneos del jugador

Se expandio la seccion de torneos del portal del jugador para mostrar estadisticas reales de participacion y rendimiento con base en inscripciones y partidas confirmadas.

### Datos visibles en la nueva vista

- torneos asistidos
- win streak actual
- W/L rate
- victorias, derrotas y empates acumulados
- historial de torneos asistidos con record, puntos y OMW
- torneos publicados disponibles para inscripcion

### Archivos clave

- `app/Http/Controllers/Web/PlayerTournamentController.php`
- `resources/views/account/tournaments.blade.php`

### Validacion aplicada

- `php -l app/Http/Controllers/Web/PlayerTournamentController.php`
- `php artisan view:cache`

## Actualizacion reciente de contenido y personalizacion del sitio

Se agrego una seccion administrativa para editar contenido visible del sitio sin tocar codigo, con acceso exclusivo para usuarios con rol `admin`.

### Alcance de la personalizacion

- nombre del sitio y subtitulo
- mensaje superior opcional del header publico
- kicker, titular y descripcion del hero principal
- encabezado y descripcion del bloque de catalogo
- tres bloques de beneficios comerciales

### Restriccion de acceso

- solo administradores pueden ver y modificar esta seccion
- managers y cashiers mantienen operacion del panel, pero sin permisos de personalizacion

### Archivos clave

- `app/Models/SiteSetting.php`
- `app/Http/Requests/SiteSettingRequest.php`
- `app/Http/Controllers/Web/SiteSettingController.php`
- `database/migrations/2026_03_27_130000_create_site_settings_table.php`
- `resources/views/site-settings/edit.blade.php`
- `routes/web.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/public.blade.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/store/index.blade.php`
- `app/Providers/AppServiceProvider.php`

### Consideraciones

- es necesario ejecutar `php artisan migrate` para crear la tabla `site_settings`
- los cambios se reflejan en la tienda publica y en el branding base del sitio

## Ajuste reciente de contraste en metricas del hero

Se mejoro la legibilidad de las tarjetas de resumen dentro del hero principal de la tienda para que se distingan mejor del fondo oscuro y entre si.

### Ajuste aplicado

- tarjeta de seleccion con tono ambar oscuro
- tarjeta de categorias con tono azul petroleo
- tarjeta de envio con tono carbon calido

### Archivo clave

- `resources/views/store/index.blade.php`

## Ajuste reciente en bloque de catalogo

Se reforzo el contraste del encabezado del catalogo publico para integrarlo mejor con la paleta oscura del sitio y evitar que el bloque se viera demasiado claro frente al resto de la pagina.

### Ajuste aplicado

- contenedor principal del catalogo con fondo oscuro, borde ambar sutil y sombra mas marcada
- tarjetas de `Resultados`, `Vista actual` y `Filtro activo` con tonos diferenciados para lectura rapida

### Archivo clave

- `resources/views/store/index.blade.php`

## Ajuste reciente de entrega de imagenes de productos

Se hizo mas robusta la resolucion de imagenes de producto para que la tienda y el panel administrativo puedan mostrarlas incluso cuando el servidor no tenga disponible `public/storage`.

### Ajuste aplicado

- resolucion centralizada de URLs de imagen en los modelos `Product` y `ProductImage`
- nueva ruta publica de respaldo para servir archivos desde el disco `public`
- galerias y vistas de detalle actualizadas para usar la URL resuelta del modelo

### Archivos clave

- `app/Http/Controllers/Web/PublicMediaController.php`
- `app/Models/Product.php`
- `app/Models/ProductImage.php`
- `routes/web.php`
- `resources/views/products/_form.blade.php`
- `resources/views/products/show.blade.php`
- `resources/views/store/show.blade.php`

### Consideraciones

- si el archivo no existe en `storage/app/public/products`, la imagen seguira sin mostrarse aunque la URL ya sea correcta
- `php artisan storage:link` sigue siendo recomendable cuando el hosting lo permite

## Modulo reciente de vlog y articulos

Se agrego un modulo editorial completo para publicar entradas desde admin y permitir que la comunidad las lea y comente desde el portal publico.

### Alcance del modulo

- CRUD de articulos exclusivo para administradores
- entradas con titulo, slug, resumen, contenido largo, portada y fecha de publicacion
- opcion para habilitar o cerrar comentarios por entrada
- listado publico de articulos y vista de detalle
- comentarios disponibles para usuarios registrados y visitantes
- revision y eliminacion de comentarios desde admin
- acceso rapido al modulo desde dashboard admin, menu admin y portal del jugador

### Archivos clave

- `app/Models/Article.php`
- `app/Models/ArticleComment.php`
- `app/Http/Controllers/Web/ArticleController.php`
- `app/Http/Controllers/Web/PublicArticleController.php`
- `app/Http/Controllers/Web/ArticleCommentController.php`
- `app/Http/Requests/Article/AdminArticleRequest.php`
- `app/Http/Requests/Article/ArticleCommentRequest.php`
- `database/migrations/2026_03_27_140000_create_articles_tables.php`
- `resources/views/articles/*`
- `resources/views/blog/*`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/public.blade.php`
- `resources/views/account/dashboard.blade.php`
- `resources/views/dashboard/index.blade.php`
- `routes/web.php`

### Consideraciones

- es necesario ejecutar `php artisan migrate` para crear las tablas `articles` y `article_comments`
- la portada usa el mismo sistema de archivos publicos y fallback de medios ya integrado en el proyecto

## Modulo reciente de redes sociales en home

Se agrego una nueva seccion social en la pagina principal para mostrar contenido embebido de Facebook, Instagram y TikTok, junto con botones para seguir las cuentas oficiales.

### Alcance del modulo

- bloque social visible en la home publica con mejor integracion visual al tema oscuro
- espacio para album/publicaciones de Facebook
- espacio para feed o publicaciones recientes de Instagram
- espacio para videos recientes de TikTok
- botones para seguir cada red social desde la home
- configuracion centralizada desde admin dentro de contenido del sitio
- acceso rapido desde el dashboard admin para conectar las cuentas

### Archivos clave

- `database/migrations/2026_03_27_150000_add_social_fields_to_site_settings_table.php`
- `app/Models/SiteSetting.php`
- `app/Http/Requests/SiteSettingRequest.php`
- `resources/views/site-settings/edit.blade.php`
- `resources/views/store/index.blade.php`
- `resources/views/dashboard/index.blade.php`

### Consideraciones

- es necesario ejecutar `php artisan migrate` para agregar los nuevos campos sociales en `site_settings`
- los embeds se pegan directamente desde admin, por lo que el contenido visible depende del codigo de embebido que entregue cada plataforma

## Ajuste reciente de deploy e interfaz movil

Se reforzo el despliegue para hosting compartido sin symlinks persistentes y se recupero la navegacion principal en movil.

### Ajuste aplicado

- nuevo `deploy.sh` para Hostinger que copia `public/` a `public_html/`
- sincronizacion fisica de `storage/app/public/` hacia `public_html/storage/` con `rsync`
- sin uso de symlinks para archivos publicos durante deploy
- menu principal movil agregado al header publico para pantallas pequenas

### Archivos clave

- `deploy.sh`
- `resources/views/layouts/public.blade.php`

### Consideraciones

- el bloque de sincronizacion de `storage` corre despues de copiar `public/*` a `public_html/`
- el script es idempotente y puede ejecutarse multiples veces sin depender de `storage:link`

## Ajuste reciente de embeds sociales

Se ajusto la ventana visible de los embeds de Facebook, Instagram y TikTok para que muestren mejor su contenido completo y se adapten mejor a movil.

### Ajuste aplicado

- contenedores responsivos por red social dentro de la home publica
- alturas minimas especificas para Facebook, Instagram y TikTok
- iframes, blockquotes y widgets limitados al ancho disponible del contenedor
- mejor comportamiento en pantallas pequenas para evitar recortes o cajas demasiado bajas

### Archivos clave

- `resources/views/store/index.blade.php`
- `resources/css/app.css`

### Consideraciones

- el resultado final sigue dependiendo del codigo embed que entregue cada plataforma
- si una red inyecta scripts externos, puede requerir unos segundos para ajustar su alto real en el navegador

## Ajuste reciente de movimientos de inventario

Se agrego una capa de trazabilidad completa para inventario, pensada para convivir con el POS local, el panel administrativo y las ventas del ecommerce sin perder auditoria historica.

### Ajuste aplicado

- nueva tabla `inventory_movements` con `uuid`, referencias opcionales a venta, dispositivo y usuario, y `sync_version`
- cada venta completada ahora genera movimientos automaticos por producto dentro del flujo de `SaleService`
- nuevo endpoint `POST /api/inventory-movements` para ajustes manuales auditados
- nuevo endpoint `GET /api/inventory-movements` con filtros por producto, fechas, tipo, fuente y dispositivo
- nuevo endpoint `POST /api/sync/upload-inventory-movements` con idempotencia por `uuid`
- el endpoint de correccion de stock `PATCH /api/products/{product}/stock` ahora crea un movimiento auditado en lugar de cambiar stock sin rastro
- se agregan logs en `sync_logs` para uploads de movimientos desde POS

### Archivos clave

- `database/migrations/2026_03_27_162000_create_inventory_movements_table.php`
- `app/Models/InventoryMovement.php`
- `app/Services/InventoryMovementService.php`
- `app/Services/SaleService.php`
- `app/Http/Controllers/Api/InventoryMovementController.php`
- `app/Http/Controllers/Api/SyncInventoryMovementController.php`
- `app/Http/Resources/InventoryMovementResource.php`
- `app/Http/Requests/Inventory/StoreInventoryMovementRequest.php`
- `app/Http/Requests/Inventory/InventoryMovementIndexRequest.php`
- `app/Http/Requests/Sync/UploadInventoryMovementsRequest.php`
- `tests/Feature/InventoryMovementTest.php`

### Consideraciones

- requiere ejecutar `php artisan migrate` para crear `inventory_movements`
- las ventas existentes no generan movimientos retroactivos; el comportamiento aplica a partir de esta version
- los tests del modulo quedaron agregados, pero en este entorno no fue posible ejecutarlos porque `php artisan test` no esta disponible y el binario de PHPUnit no viene expuesto en `vendor/bin`

## Ajuste reciente de movimientos de inventario

Se agrego una capa de trazabilidad completa para inventario, pensada para convivir con el POS local, el panel administrativo y las ventas del ecommerce sin perder auditoria historica.

### Ajuste aplicado

- nueva tabla `inventory_movements` con `uuid`, referencias opcionales a venta, dispositivo y usuario, y `sync_version`
- cada venta completada ahora genera movimientos automaticos por producto dentro del flujo de `SaleService`
- nuevo endpoint `POST /api/inventory-movements` para ajustes manuales auditados
- nuevo endpoint `GET /api/inventory-movements` con filtros por producto, fechas, tipo, fuente y dispositivo
- nuevo endpoint `POST /api/sync/upload-inventory-movements` con idempotencia por `uuid`
- el endpoint de correccion de stock `PATCH /api/products/{product}/stock` ahora crea un movimiento auditado en lugar de cambiar stock sin rastro
- se agregan logs en `sync_logs` para uploads de movimientos desde POS

### Archivos clave

- `database/migrations/2026_03_27_162000_create_inventory_movements_table.php`
- `app/Models/InventoryMovement.php`
- `app/Services/InventoryMovementService.php`
- `app/Services/SaleService.php`
- `app/Http/Controllers/Api/InventoryMovementController.php`
- `app/Http/Controllers/Api/SyncInventoryMovementController.php`
- `app/Http/Resources/InventoryMovementResource.php`
- `app/Http/Requests/Inventory/StoreInventoryMovementRequest.php`
- `app/Http/Requests/Inventory/InventoryMovementIndexRequest.php`
- `app/Http/Requests/Sync/UploadInventoryMovementsRequest.php`
- `tests/Feature/InventoryMovementTest.php`

### Consideraciones

- requiere ejecutar `php artisan migrate` para crear `inventory_movements`
- las ventas existentes no generan movimientos retroactivos; el comportamiento aplica a partir de esta version
- los tests del modulo quedaron agregados, pero en este entorno no fue posible ejecutarlos porque `php artisan test` no esta disponible y el binario de PHPUnit no viene expuesto en `vendor/bin`

## Ajuste reciente de sincronizacion batch de catalogo

Se agrego un endpoint batch para que el POS pueda descargar el catalogo necesario en una sola llamada, con soporte para sincronizacion incremental y una respuesta compacta por entidad.

### Ajuste aplicado

- nuevo endpoint `GET /api/sync/catalog`
- soporte para `updated_since` e `include[]` con `products`, `categories`, `customers` y `settings`
- si no se envia `include`, el endpoint regresa `products`, `categories` y `customers`
- productos incluyen categoria e imagenes en forma de URLs utiles para POS, sin blobs
- categorias y configuracion del sitio quedaron listas para sync con `uuid` y `sync_version`
- la respuesta incluye metadata global de sincronizacion con tiempo del servidor y conteos por entidad

### Archivos clave

- `app/Http/Controllers/Api/SyncController.php`
- `app/Http/Requests/Sync/SyncCatalogRequest.php`
- `app/Services/Sync/SyncCatalogService.php`
- `app/Http/Resources/PosSiteSettingResource.php`
- `app/Http/Resources/CategoryResource.php`
- `app/Http/Resources/ProductResource.php`
- `app/Models/Category.php`
- `app/Models/SiteSetting.php`
- `routes/api.php`
- `database/migrations/2026_03_27_161000_add_sync_fields_to_categories_and_site_settings.php`

### Consideraciones

- requiere ejecutar `php artisan migrate` para agregar campos de sync en `categories` y `site_settings`
- el endpoint queda protegido con `auth:sanctum` y la capa actual de permisos API
- no rompe los endpoints previos `GET /api/sync/products` ni `GET /api/sync/customers`

## Ajuste reciente de sincronizacion batch de catalogo

Se agrego un endpoint batch para que el POS pueda descargar el catalogo necesario en una sola llamada, con soporte para sincronizacion incremental y una respuesta compacta por entidad.

### Ajuste aplicado

- nuevo endpoint `GET /api/sync/catalog`
- soporte para `updated_since` e `include[]` con `products`, `categories`, `customers` y `settings`
- si no se envia `include`, el endpoint regresa `products`, `categories` y `customers`
- productos incluyen categoria e imagenes en forma de URLs utiles para POS, sin blobs
- categorias y configuracion del sitio quedaron listas para sync con `uuid` y `sync_version`
- la respuesta incluye metadata global de sincronizacion con tiempo del servidor y conteos por entidad

### Archivos clave

- `app/Http/Controllers/Api/SyncController.php`
- `app/Http/Requests/Sync/SyncCatalogRequest.php`
- `app/Services/Sync/SyncCatalogService.php`
- `app/Http/Resources/PosSiteSettingResource.php`
- `app/Http/Resources/CategoryResource.php`
- `app/Http/Resources/ProductResource.php`
- `app/Models/Category.php`
- `app/Models/SiteSetting.php`
- `routes/api.php`
- `database/migrations/2026_03_27_161000_add_sync_fields_to_categories_and_site_settings.php`

### Consideraciones

- requiere ejecutar `php artisan migrate` para agregar campos de sync en `categories` y `site_settings`
- el endpoint queda protegido con `auth:sanctum` y la capa actual de permisos API
- no rompe los endpoints previos `GET /api/sync/products` ni `GET /api/sync/customers`

## Ajuste reciente de base de sincronizacion offline-first

Se completo una base mas consistente para integrar el servidor con un POS local offline-first, manteniendo compatibilidad con los endpoints de sincronizacion ya existentes.

### Ajuste aplicado

- se agrego `sync_version` en `products`, `customers`, `sales` y `devices`
- se centralizo el versionado automatico con un trait reusable para incrementarlo en altas y cambios relevantes
- se unifico el contrato de respuesta API con `success`, `message`, `data`, `meta` y `errors`
- se agrego soporte consistente para `updated_since`, `per_page`, `cursor` e `include_deleted`
- se separo la logica de sync en servicios para authority, consultas, heartbeat y carga de ventas
- el catalogo queda con autoridad del servidor y la carga de ventas del POS acepta la venta si su `uuid` no existe aun
- los recursos sincronizables ya regresan `uuid`, fechas, `deleted_at`, `sync_version` e `is_active` o equivalente

### Archivos clave

- `database/migrations/2026_03_27_160000_add_sync_version_to_sync_tables.php`
- `app/Models/Concerns/HasSyncVersion.php`
- `app/Support/ApiResponse.php`
- `app/Http/Requests/Sync/SyncIndexRequest.php`
- `app/Http/Requests/Sync/UploadSalesRequest.php`
- `app/Services/Sync/SyncAuthorityService.php`
- `app/Services/Sync/SyncQueryService.php`
- `app/Services/Sync/SyncHeartbeatService.php`
- `app/Services/Sync/SyncSaleUploadService.php`
- `app/Http/Controllers/Api/SyncController.php`

### Endpoints vigentes

- `POST /api/sync/heartbeat`
- `GET /api/sync/products`
- `GET /api/sync/customers`
- `POST /api/sync/upload-sales`

### Consideraciones

- requiere ejecutar `php artisan migrate` para agregar `sync_version`
- los endpoints existentes se mantienen
- `GET /api/sync/products` y `GET /api/sync/customers` ahora aceptan filtros consistentes de sincronizacion
- `POST /api/sync/upload-sales` sigue aceptando `product_id`, pero ahora tambien puede resolver productos por `product_uuid`, `product_sku` o `product_barcode`

## Ajuste reciente de conflictos, bajas logicas e idempotencia avanzada

Se endurecio la capa offline-first para que el POS pueda reintentar sincronizaciones sin duplicar datos, detectar bajas logicas y entender conflictos por item de forma clara.

### Ajuste aplicado

- `categories` ahora usa `soft deletes`, alineandose con `products` y `customers`
- los recursos de sync para catalogo exponen `deleted_at`, `is_active`, `sync_version`, `client_generated_at` y `received_at` donde aplica
- se centralizaron las reglas de conflicto en una capa dedicada para ventas y movimientos de inventario
- los uploads batch ya regresan respuesta por item con `uuid`, `status`, `code`, `message`, `server_entity` y `errors`
- los reintentos con el mismo `uuid` ahora regresan `skipped` de forma consistente
- las referencias rotas a producto, cliente, usuario, venta o dispositivo se clasifican como conflicto sin tumbar el lote completo
- las respuestas batch incluyen un resumen por estado para simplificar la logica del POS
- la guia de integracion y reglas de autoridad/conflicto quedo documentada dentro del repo

### Politicas de autoridad

- catalogo: `server wins`
- clientes descargados por sync: `server wins`
- ventas subidas por POS: aceptadas si el `uuid` no existe en servidor
- movimientos de inventario subidos por POS: aceptados si el `uuid` no existe en servidor

### Archivos clave

- `database/migrations/2026_03_27_163000_add_soft_deletes_to_categories_and_sync_receipts.php`
- `app/Models/Concerns/HasSyncVersion.php`
- `app/Services/Sync/SyncConflictResolver.php`
- `app/Services/Sync/SyncBatchResultService.php`
- `app/Services/Sync/SyncSaleUploadService.php`
- `app/Services/InventoryMovementService.php`
- `app/Support/SyncConflictException.php`
- `app/Support/SyncReferenceException.php`
- `docs/sync-conflict-rules.md`
- `tests/Feature/SyncConflictTest.php`

### Consideraciones

- requiere ejecutar `php artisan migrate` para agregar `soft deletes` en `categories` y timestamps de sync en `sales` e `inventory_movements`
- `php artisan test` no pudo ejecutarse en este entorno por restricciones del runner local y permisos sobre `storage/logs/laravel.log`
- las reglas de conflicto para futuros cierres de caja quedaron preparadas en la capa de autoridad, pero aun no existe un endpoint real de `upload-cash-closures` en el proyecto

## Ajuste reciente de cierre QA para integracion POS

Se completo el lado servidor para pruebas reales de integracion con POS local, agregando cobertura automatizada, observabilidad coherente y documentacion tecnica operativa.

### Ajuste aplicado

- nuevo endpoint `POST /api/sync/upload-cash-closures`
- nueva tabla `cash_closures` con `uuid`, referencias a dispositivo/usuario, montos de apertura/cierre, diferencias y campos de sync
- logs de sync mas claros en `SyncLogService` con eventos `info`, `warning` y `error` segun el resultado del procesamiento
- documentacion tecnica centralizada para POS en `docs/pos-sync-api.md`
- base de fixtures reutilizable para tests de sincronizacion y autenticacion API
- suite feature cubriendo auth, lecturas de catalogo y uploads batch con casos de exito, validacion, duplicados y referencias rotas
- correccion de una inconsistencia historica en la migracion de `site_settings` que estaba rompiendo la suite de pruebas

### Cobertura automatizada

- `tests/Feature/ApiAuthTest.php`
- `tests/Feature/SyncReadEndpointsTest.php`
- `tests/Feature/SyncUploadEndpointsTest.php`
- `tests/Feature/InventoryMovementTest.php`
- `tests/Feature/SyncConflictTest.php`
- `tests/Concerns/CreatesSyncFixtures.php`

### Endpoints POS cubiertos

- `POST /api/auth/login`
- `GET /api/sync/products`
- `GET /api/sync/customers`
- `GET /api/sync/catalog`
- `POST /api/sync/upload-sales`
- `POST /api/sync/upload-cash-closures`
- `POST /api/sync/upload-inventory-movements`

### Validacion aplicada

- `php artisan test`
- `php artisan route:list --path=api/sync`
- `php artisan route:list --path=api/auth`
- `php -l` en modelos, requests, servicios, controladores, migraciones y tests nuevos

### Archivos clave

- `database/migrations/2026_03_27_164000_create_cash_closures_table.php`
- `app/Models/CashClosure.php`
- `app/Http/Requests/Sync/UploadCashClosuresRequest.php`
- `app/Http/Controllers/Api/SyncCashClosureController.php`
- `app/Services/Sync/SyncCashClosureUploadService.php`
- `app/Services/SyncLogService.php`
- `docs/pos-sync-api.md`
- `phpunit.xml`

### Consideraciones

- requiere ejecutar `php artisan migrate` para crear `cash_closures`
- el backend ya queda con pruebas verdes para los flujos clave de integracion POS
- todavia conviene limpiar warnings de autoload ambiguo en `vendor` en una pasada de mantenimiento aparte

## Ajuste reciente de preventas y abonos

Se agrego un modulo backend de preventas para reservar productos, registrar abonos parciales y dejar el dominio listo para integracion futura con POS local y tienda web.

### Ajuste aplicado

- nuevas tablas `preorders`, `preorder_items` y `preorder_payments`
- cada preventa usa `uuid`, `preorder_number`, `source` y `sync_version`
- soporte para multiples items por preventa con snapshot de producto (`product_name`, `product_uuid`, `product_sku`)
- registro de abonos parciales con `method`, `amount`, `reference`, `notes` y `paid_at`
- recalculo automatico de `amount_paid`, `amount_due` y `status`
- endpoint de sync de lectura `GET /api/sync/preorders` para futura consulta incremental desde POS

### Estados soportados

- `pending`
- `partially_paid`
- `paid`
- `cancelled`
- `delivered`

### Endpoints agregados

- `GET /api/preorders`
- `POST /api/preorders`
- `GET /api/preorders/{preorder}`
- `POST /api/preorders/{preorder}/payments`
- `GET /api/sync/preorders`

### Archivos clave

- `database/migrations/2026_03_27_165000_create_preorders_tables.php`
- `app/Models/Preorder.php`
- `app/Models/PreorderItem.php`
- `app/Models/PreorderPayment.php`
- `app/Services/PreorderService.php`
- `app/Http/Controllers/Api/PreorderController.php`
- `app/Http/Requests/Preorder/StorePreorderRequest.php`
- `app/Http/Requests/Preorder/StorePreorderPaymentRequest.php`
- `app/Http/Resources/PreorderResource.php`
- `app/Http/Resources/PreorderItemResource.php`
- `app/Http/Resources/PreorderPaymentResource.php`
- `app/Http/Controllers/Api/SyncController.php`
- `routes/api.php`

### Validacion aplicada

- `php -l` en migracion, modelos, requests, resources, servicio y controladores
- `php artisan route:list --path=api/preorders`
- `php artisan route:list --path=api/sync`

### Consideraciones

- requiere ejecutar `php artisan migrate` para crear las tablas de preventas
- no se corrio la suite completa despues de este modulo en este entorno, asi que conviene validar con `php artisan test`

## Ajuste reciente de registro web de jugadores

Se corrigio el alta publica de cuentas de jugador para que el campo `name` del formulario llegue correctamente al modelo `User` y deje de fallar con `Column 'name' cannot be null`.

### Ajuste aplicado

- correccion del mapeo del campo validado en el controlador web de autenticacion
- el registro ahora usa `name` en lugar de un typo que estaba leyendo `namee`

### Archivo clave

- `app/Http/Controllers/Web/AuthController.php`

### Validacion aplicada

- `php -l app/Http/Controllers/Web/AuthController.php`

## Ajuste reciente de preventas y abonos

Se agrego un modulo backend de preventas para reservar productos, registrar abonos parciales y dejar el dominio listo para integracion futura con POS local y tienda web.

### Ajuste aplicado

- nuevas tablas `preorders`, `preorder_items` y `preorder_payments`
- cada preventa usa `uuid`, `preorder_number`, `source` y `sync_version`
- soporte para multiples items por preventa con snapshot de producto (`product_name`, `product_uuid`, `product_sku`)
- registro de abonos parciales con `method`, `amount`, `reference`, `notes` y `paid_at`
- recalculo automatico de `amount_paid`, `amount_due` y `status`
- endpoint de sync de lectura `GET /api/sync/preorders` para futura consulta incremental desde POS

### Estados soportados

- `pending`
- `partially_paid`
- `paid`
- `cancelled`
- `delivered`

### Endpoints agregados

- `GET /api/preorders`
- `POST /api/preorders`
- `GET /api/preorders/{preorder}`
- `POST /api/preorders/{preorder}/payments`
- `GET /api/sync/preorders`

### Archivos clave

- `database/migrations/2026_03_27_165000_create_preorders_tables.php`
- `app/Models/Preorder.php`
- `app/Models/PreorderItem.php`
- `app/Models/PreorderPayment.php`
- `app/Services/PreorderService.php`
- `app/Http/Controllers/Api/PreorderController.php`
- `app/Http/Requests/Preorder/StorePreorderRequest.php`
- `app/Http/Requests/Preorder/StorePreorderPaymentRequest.php`
- `app/Http/Resources/PreorderResource.php`
- `app/Http/Resources/PreorderItemResource.php`
- `app/Http/Resources/PreorderPaymentResource.php`
- `app/Http/Controllers/Api/SyncController.php`
- `routes/api.php`

### Validacion aplicada

- `php -l` en migracion, modelos, requests, resources, servicio y controladores
- `php artisan route:list --path=api/preorders`
- `php artisan route:list --path=api/sync`

### Consideraciones

- requiere ejecutar `php artisan migrate` para crear las tablas de preventas
- no se corrio la suite completa despues de este modulo en este entorno, asi que conviene validar con `php artisan test`

## Ajuste reciente de cierre QA para integracion POS

Se completo el lado servidor para pruebas reales de integracion con POS local, agregando cobertura automatizada, observabilidad coherente y documentacion tecnica operativa.

### Ajuste aplicado

- nuevo endpoint `POST /api/sync/upload-cash-closures`
- nueva tabla `cash_closures` con `uuid`, referencias a dispositivo/usuario, montos de apertura/cierre, diferencias y campos de sync
- logs de sync mas claros en `SyncLogService` con eventos `info`, `warning` y `error` segun el resultado del procesamiento
- documentacion tecnica centralizada para POS en `docs/pos-sync-api.md`
- base de fixtures reutilizable para tests de sincronizacion y autenticacion API
- suite feature cubriendo auth, lecturas de catalogo y uploads batch con casos de exito, validacion, duplicados y referencias rotas
- correccion de una inconsistencia historica en la migracion de `site_settings` que estaba rompiendo la suite de pruebas

### Cobertura automatizada

- `tests/Feature/ApiAuthTest.php`
- `tests/Feature/SyncReadEndpointsTest.php`
- `tests/Feature/SyncUploadEndpointsTest.php`
- `tests/Feature/InventoryMovementTest.php`
- `tests/Feature/SyncConflictTest.php`
- `tests/Concerns/CreatesSyncFixtures.php`

### Endpoints POS cubiertos

- `POST /api/auth/login`
- `GET /api/sync/products`
- `GET /api/sync/customers`
- `GET /api/sync/catalog`
- `POST /api/sync/upload-sales`
- `POST /api/sync/upload-cash-closures`
- `POST /api/sync/upload-inventory-movements`

### Validacion aplicada

- `php artisan test`
- `php artisan route:list --path=api/sync`
- `php artisan route:list --path=api/auth`
- `php -l` en modelos, requests, servicios, controladores, migraciones y tests nuevos

### Archivos clave

- `database/migrations/2026_03_27_164000_create_cash_closures_table.php`
- `app/Models/CashClosure.php`
- `app/Http/Requests/Sync/UploadCashClosuresRequest.php`
- `app/Http/Controllers/Api/SyncCashClosureController.php`
- `app/Services/Sync/SyncCashClosureUploadService.php`
- `app/Services/SyncLogService.php`
- `docs/pos-sync-api.md`
- `phpunit.xml`

### Consideraciones

- requiere ejecutar `php artisan migrate` para crear `cash_closures`
- el backend ya queda con pruebas verdes para los flujos clave de integracion POS
- todavia conviene limpiar warnings de autoload ambiguo en `vendor` en una pasada de mantenimiento aparte

## Ajuste reciente de base de sincronizacion offline-first

Se completo una base mas consistente para integrar el servidor con un POS local offline-first, manteniendo compatibilidad con los endpoints de sincronizacion ya existentes.

### Ajuste aplicado

- se agrego `sync_version` en `products`, `customers`, `sales` y `devices`
- se centralizo el versionado automatico con un trait reusable para incrementarlo en altas y cambios relevantes
- se unifico el contrato de respuesta API con `success`, `message`, `data`, `meta` y `errors`
- se agrego soporte consistente para `updated_since`, `per_page`, `cursor` e `include_deleted`
- se separo la logica de sync en servicios para authority, consultas, heartbeat y carga de ventas
- el catalogo queda con autoridad del servidor y la carga de ventas del POS acepta la venta si su `uuid` no existe aun
- los recursos sincronizables ya regresan `uuid`, fechas, `deleted_at`, `sync_version` e `is_active` o equivalente

### Archivos clave

- `database/migrations/2026_03_27_160000_add_sync_version_to_sync_tables.php`
- `app/Models/Concerns/HasSyncVersion.php`
- `app/Support/ApiResponse.php`
- `app/Http/Requests/Sync/SyncIndexRequest.php`
- `app/Http/Requests/Sync/UploadSalesRequest.php`
- `app/Services/Sync/SyncAuthorityService.php`
- `app/Services/Sync/SyncQueryService.php`
- `app/Services/Sync/SyncHeartbeatService.php`
- `app/Services/Sync/SyncSaleUploadService.php`
- `app/Http/Controllers/Api/SyncController.php`

### Endpoints vigentes

- `POST /api/sync/heartbeat`
- `GET /api/sync/products`
- `GET /api/sync/customers`
- `POST /api/sync/upload-sales`

### Consideraciones

- requiere ejecutar `php artisan migrate` para agregar `sync_version`
- los endpoints existentes se mantienen
- `GET /api/sync/products` y `GET /api/sync/customers` ahora aceptan filtros consistentes de sincronizacion
- `POST /api/sync/upload-sales` sigue aceptando `product_id`, pero ahora tambien puede resolver productos por `product_uuid`, `product_sku` o `product_barcode`

## Ajuste reciente de endurecimiento para sincronizacion POS

Se reforzo la API para dejar el servidor mejor preparado para sincronizar con un POS local sin exponer inventario, ventas o dispositivos a cualquier usuario autenticado.

### Ajuste aplicado

- el login API ahora solo emite tokens a usuarios de backoffice con rol `admin`, `manager` o `cashier`
- los tokens Sanctum se generan con abilities segun el rol para separar lectura, escritura, stock, pagos y sincronizacion
- `api/sync/heartbeat` ya no queda publico y ahora requiere autenticacion con token valido
- la carga de ventas del POS ya puede resolver productos por `product_uuid`, `product_sku` o `product_barcode`
- la carga de ventas tambien puede relacionar clientes y usuarios por UUID o datos estables ademas del ID interno
- el proceso de `upload-sales` ahora devuelve fallos por venta individual sin depender solo de excepciones de negocio

### Archivos clave

- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Api/SyncController.php`
- `app/Http/Requests/Sync/UploadSalesRequest.php`
- `app/Http/Middleware/EnsureApiUserCanAccessPos.php`
- `app/Http/Middleware/EnsureApiTokenHasAbility.php`
- `routes/api.php`
- `bootstrap/app.php`

### Consideraciones

- no requiere migraciones nuevas
- el POS debe autenticarse primero y reutilizar el bearer token en `heartbeat`, lecturas de sync y `upload-sales`
- para una sincronizacion todavia mas avanzada de inventario entre multiples nodos, en una siguiente fase conviene agregar versionado o ledger de movimientos

## Ajuste reciente de sincronizacion bidireccional de productos POS

Se agrego el endpoint servidor `POST /api/sync/upload-products` para que el POS local pueda crear o actualizar productos y recibir de vuelta el `remote_id` persistido en Laravel.

### Regla de matching aplicada

- primero intenta por `remote_id`
- si no existe, intenta por `sku`
- si no existe, intenta por `barcode`
- si no hay match, crea el producto

### Comportamiento clave

- cada item del lote se procesa de forma independiente
- si `active = 0`, el producto solo se marca como inactivo
- la respuesta devuelve `local_id`, `remote_id` y `product.id`
- errores por item responden `status = error` sin romper el lote completo

### Archivos clave

- `routes/api.php`
- `app/Http/Controllers/Api/SyncProductsController.php`
- `app/Http/Requests/Sync/UploadProductsRequest.php`
- `app/Services/Sync/SyncProductUploadService.php`
- `app/Models/Product.php`
- `database/migrations/2026_03_28_120000_add_pos_sync_fields_to_products_table.php`
- `docs/pos-sync-api.md`

### Consideraciones

- requiere ejecutar `php artisan migrate` para agregar los nuevos campos de producto orientados a POS
- los tokens existentes deben incluir la ability `sync:upload-products` para usar el nuevo endpoint

## Ajuste reciente del modulo admin de torneos

Se restauraron las vistas faltantes del panel de administracion para que el modulo de torneos vuelva a funcionar correctamente y ya no falle con `View [tournaments.index] not found`.

### Ajuste aplicado

- se recrearon las vistas `index`, `create`, `edit` y `show` del modulo admin de torneos
- se agrego un parcial reutilizable para el formulario de alta y edicion
- la vista de detalle muestra resumen del torneo, standings, registros, rondas y reporte de resultados

### Archivos clave

- `resources/views/tournaments/index.blade.php`
- `resources/views/tournaments/create.blade.php`
- `resources/views/tournaments/edit.blade.php`
- `resources/views/tournaments/show.blade.php`
- `resources/views/tournaments/_form.blade.php`

### Consideraciones

- no requiere migraciones nuevas
- el controlador `TournamentController` ya puede resolver sus vistas esperadas sin errores en produccion

## Ajuste reciente critico de deploy en Hostinger

Se corrigio el despliegue para que `public_html` reciba todos los archivos reales de `public/`, incluyendo archivos ocultos necesarios para Apache como `.htaccess`.

### Ajuste aplicado

- reemplazo de la copia simple `cp -r public/* public_html/` por `rsync -av --delete public/ public_html/`
- limpieza completa previa de `public_html` antes de reconstruirla
- conservacion del flujo de sincronizacion de `storage/app/public` hacia `public_html/storage`

### Archivo clave

- `deploy.sh`

### Consideraciones

- este ajuste evita que el sitio quede sin reglas de reescritura por no copiar `.htaccess`
- el siguiente deploy en Hostinger debe reconstruir correctamente la carpeta publica

## Ajuste reciente critico de deploy en Hostinger

Se corrigio el despliegue para que `public_html` reciba todos los archivos reales de `public/`, incluyendo archivos ocultos necesarios para Apache como `.htaccess`.

### Ajuste aplicado

- reemplazo de la copia simple `cp -r public/* public_html/` por `rsync -av --delete public/ public_html/`
- limpieza completa previa de `public_html` antes de reconstruirla
- conservacion del flujo de sincronizacion de `storage/app/public` hacia `public_html/storage`

### Archivo clave

- `deploy.sh`

### Consideraciones

- este ajuste evita que el sitio quede sin reglas de reescritura por no copiar `.htaccess`
- el siguiente deploy en Hostinger debe reconstruir correctamente la carpeta publica

## Ajuste reciente de home y tienda separadas

Se dividio la experiencia publica para que la portada principal ya no mezcle branding con catalogo y la tienda tenga ahora su propia vista dedicada.

### Ajuste aplicado

- `/` ahora funciona como home institucional/comunidad
- `/tienda` concentra catalogo, filtros, busqueda y productos
- nueva vista publica exclusiva para la portada principal
- el catalogo existente se mantiene sin perder funciones de carrito o detalle de producto

### Archivos clave

- `app/Http/Controllers/Web/StorefrontController.php`
- `routes/web.php`
- `resources/views/store/home.blade.php`
- `resources/views/store/index.blade.php`

### Consideraciones

- los enlaces principales siguen apuntando correctamente a `store.home` y `store.catalog`
- no requiere migraciones nuevas

## Ajuste reciente de home y tienda separadas

Se dividio la experiencia publica para que la portada principal ya no mezcle branding con catalogo y la tienda tenga ahora su propia vista dedicada.

### Ajuste aplicado

- `/` ahora funciona como home institucional/comunidad
- `/tienda` concentra catalogo, filtros, busqueda y productos
- nueva vista publica exclusiva para la portada principal
- el catalogo existente se mantiene sin perder funciones de carrito o detalle de producto

### Archivos clave

- `app/Http/Controllers/Web/StorefrontController.php`
- `routes/web.php`
- `resources/views/store/home.blade.php`
- `resources/views/store/index.blade.php`

### Consideraciones

- los enlaces principales siguen apuntando correctamente a `store.home` y `store.catalog`
- no requiere migraciones nuevas

## Ajuste reciente de embeds sociales

Se ajusto la ventana visible de los embeds de Facebook, Instagram y TikTok para que muestren mejor su contenido completo y se adapten mejor a movil.

### Ajuste aplicado

- contenedores responsivos por red social dentro de la home publica
- alturas minimas especificas para Facebook, Instagram y TikTok
- iframes, blockquotes y widgets limitados al ancho disponible del contenedor
- mejor comportamiento en pantallas pequenas para evitar recortes o cajas demasiado bajas

### Archivos clave

- `resources/views/store/index.blade.php`
- `resources/css/app.css`

### Consideraciones

- el resultado final sigue dependiendo del codigo embed que entregue cada plataforma
- si una red inyecta scripts externos, puede requerir unos segundos para ajustar su alto real en el navegador

## Ajuste reciente de embeds sociales

Se ajusto la ventana visible de los embeds de Facebook, Instagram y TikTok para que muestren mejor su contenido completo y se adapten mejor a movil.

### Ajuste aplicado

- contenedores responsivos por red social dentro de la home publica
- alturas minimas especificas para Facebook, Instagram y TikTok
- iframes, blockquotes y widgets limitados al ancho disponible del contenedor
- mejor comportamiento en pantallas pequenas para evitar recortes o cajas demasiado bajas

### Archivos clave

- `resources/views/store/index.blade.php`
- `resources/css/app.css`

### Consideraciones

- el resultado final sigue dependiendo del codigo embed que entregue cada plataforma
- si una red inyecta scripts externos, puede requerir unos segundos para ajustar su alto real en el navegador
