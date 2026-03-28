# POS Sync API

## Proposito

Este modulo expone la capa de sincronizacion entre el servidor `cardbastion-server` y un POS local offline-first. El servidor sigue siendo la fuente maestra del catalogo para descargas, pero ahora tambien acepta cargas bidireccionales de productos desde el POS por lote mediante referencias estables.

## Flujo general

1. El POS inicia sesion con `POST /api/auth/login`.
2. Guarda el bearer token de Sanctum.
3. Reporta presencia con `POST /api/sync/heartbeat`.
4. Descarga catalogo con `GET /api/sync/catalog` o por recurso con `GET /api/sync/products` y `GET /api/sync/customers`.
5. Sube productos, ventas, cierres y movimientos en lotes.
6. Interpreta la respuesta por item segun el dominio sincronizado.

## Auth

- Esquema: `Bearer <token>`
- Endpoint de login: `POST /api/auth/login`
- Roles con acceso API POS: `admin`, `manager`, `cashier`
- Los tokens usan abilities por dominio

### Login ejemplo

```json
{
  "email": "admin@example.com",
  "password": "password",
  "device_name": "POS Local"
}
```

## Endpoints

### `GET /api/sync/products`

Parametros:

- `updated_since`
- `per_page`
- `cursor`
- `include_deleted`

### `GET /api/sync/customers`

Parametros:

- `updated_since`
- `per_page`
- `cursor`
- `include_deleted`

### `GET /api/sync/catalog`

Parametros:

- `updated_since`
- `include[]=products`
- `include[]=categories`
- `include[]=customers`
- `include[]=settings`

Si no se manda `include`, devuelve `products`, `categories` y `customers`.

### `POST /api/sync/upload-sales`

```json
{
  "device_code": "POS-01",
  "sales": [
    {
      "uuid": "b55e62ad-0c1d-4ff3-9d13-8f8d01fca12d",
      "user_uuid": "2e6d07f2-ef52-47fb-a7ff-fd5b1486d9f8",
      "status": "completed",
      "client_generated_at": "2026-03-27T12:00:00Z",
      "items": [
        {
          "product_uuid": "db1577f2-0cc7-4c6f-9d25-a034a8a2e96c",
          "quantity": 2,
          "unit_price": 120
        }
      ]
    }
  ]
}
```

### `POST /api/sync/upload-products`

Este endpoint trata al POS como emisor maestro de altas y cambios de producto. Cada item se procesa de forma independiente y responde con `local_id`, `remote_id` y una copia resumida del producto persistido en servidor.

Orden exacto de matching por item:

1. `product.remote_id`
2. `product.sku`
3. `product.barcode`
4. si no hay coincidencia, se crea el producto

Si `active = 0`, el producto no se elimina: solo se marca como inactivo.

```json
{
  "store_id": "sucursal-centro",
  "device_code": "POS-LOCAL-01",
  "products": [
    {
      "local_id": 12,
      "event_type": "product.create",
      "action": "create",
      "product": {
        "remote_id": null,
        "sku": "ABC-123",
        "barcode": "7501234567890",
        "name": "Producto ejemplo",
        "category": "Accesorios",
        "price": 99,
        "cost": 50,
        "stock": 10,
        "min_stock": 2,
        "image": null,
        "active": 1,
        "product_type": "normal",
        "game": null,
        "card_name": null,
        "set_name": null,
        "set_code": null,
        "collector_number": null,
        "finish": null,
        "language": null,
        "card_condition": null,
        "created_at": "2026-03-28T10:00:00Z",
        "updated_at": "2026-03-28T10:00:00Z"
      }
    }
  ]
}
```

Respuesta esperada:

```json
{
  "success": true,
  "results": [
    {
      "status": "created",
      "local_id": 12,
      "remote_id": 345,
      "product": {
        "id": 345,
        "remote_id": 345,
        "sku": "ABC-123",
        "barcode": "7501234567890",
        "name": "Producto ejemplo",
        "category": "Accesorios",
        "price": 99,
        "cost": 50,
        "stock": 10,
        "min_stock": 2,
        "active": 1,
        "product_type": "normal",
        "updated_at": "2026-03-28T10:00:05Z"
      }
    }
  ]
}
```

Error por item:

```json
{
  "success": true,
  "results": [
    {
      "status": "error",
      "local_id": 12,
      "remote_id": null,
      "message": "SQLSTATE[23000]: Integrity constraint violation ..."
    }
  ]
}
```

### `POST /api/sync/upload-cash-closures`

```json
{
  "device_code": "POS-01",
  "closures": [
    {
      "uuid": "5c7c0bb4-bbb3-4993-b2f2-7ae9325342a6",
      "user_uuid": "2e6d07f2-ef52-47fb-a7ff-fd5b1486d9f8",
      "opening_amount": 100,
      "cash_sales": 250,
      "card_sales": 500,
      "closing_amount": 350,
      "status": "closed",
      "source": "pos",
      "client_generated_at": "2026-03-27T23:00:00Z"
    }
  ]
}
```

### `POST /api/sync/upload-inventory-movements`

```json
{
  "device_code": "POS-01",
  "movements": [
    {
      "uuid": "c446244f-921a-4cec-90ee-27055a67ea3d",
      "product_uuid": "db1577f2-0cc7-4c6f-9d25-a034a8a2e96c",
      "movement_type": "restock",
      "direction": "in",
      "quantity": 5,
      "source": "pos"
    }
  ]
}
```

## Respuesta batch

```json
{
  "success": true,
  "message": "Proceso de sincronizacion completado.",
  "data": [
    {
      "uuid": "b55e62ad-0c1d-4ff3-9d13-8f8d01fca12d",
      "status": "created",
      "code": null,
      "message": "Venta sincronizada correctamente.",
      "server_entity": {},
      "errors": []
    }
  ],
  "meta": {
    "domain": "sales_upload",
    "winner": "pos",
    "rule": "pos_sale_is_accepted_when_uuid_is_missing_on_server",
    "summary": {
      "total": 1,
      "created": 1,
      "updated": 0,
      "skipped": 0,
      "conflict": 0,
      "failed": 0
    },
    "server_time": "2026-03-27T18:00:00Z"
  },
  "errors": []
}
```

## Reglas de autoridad

- Catalogo: `server wins`
- Clientes descargados por sync: `server wins`
- Productos subidos por POS: se actualizan o crean por `remote_id`, `sku`, `barcode`
- Ventas subidas por POS: aceptadas si el `uuid` no existe
- Cierres subidos por POS: aceptados si el `uuid` no existe
- Movimientos subidos por POS: aceptados si el `uuid` no existe

## Tabla de status batch

| Status | Significado | Accion POS |
| --- | --- | --- |
| `created` | El item fue persistido | Marcar sincronizado |
| `updated` | Reservado para reconciliaciones seguras futuras | Reemplazar con version servidor |
| `skipped` | El `uuid` ya existia | No reintentar |
| `conflict` | Regla de negocio o referencia rota | Corregir y reintentar |
| `failed` | Error inesperado servidor | Reintentar tecnicamente |

### Status de `upload-products`

| Status | Significado | Accion POS |
| --- | --- | --- |
| `created` | El producto no existia y fue creado | Guardar `remote_id` |
| `updated` | El producto se encontro y fue actualizado | Actualizar referencia local |
| `error` | Ese item fallo, pero el lote siguio | Corregir y reenviar solo el item fallido |

## Referencias y conflictos comunes

- `missing_device`
- `missing_product`
- `missing_customer`
- `missing_user`
- `missing_sale`
- `duplicate_sale_number`

## Checklist de integracion POS

- Generar y persistir `uuid` local para ventas, cierres y movimientos antes de sincronizar.
- Conservar `client_generated_at`.
- Usar `device_code` estable por instalacion POS.
- Guardar el bearer token y renovarlo cuando expire o se invalide.
- Descargar catalogo incremental con `updated_since`.
- Tratar `deleted_at != null` como baja logica remota.
- Tratar `is_active = false` como desactivacion.
- Guardar `remote_id` devuelto por `POST /api/sync/upload-products`.
- No reintentar items `skipped`.
- Si llega `conflict`, revisar `code`, `errors` y `server_entity`.
