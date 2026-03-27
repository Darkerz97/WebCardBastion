# Reglas de Sincronizacion Offline-First

## Autoridad por dominio

- Catalogo (`products`, `categories`, `site settings`): `server wins`
- Clientes descargados por sync: `server wins`
- Ventas subidas por POS: se aceptan si el `uuid` no existe en el servidor
- Movimientos de inventario subidos por POS: se aceptan si el `uuid` no existe en el servidor

## Soft deletes y desactivacion

- `products`, `customers` y `categories` usan bajas logicas.
- Los endpoints de sync deben incluir `deleted_at` cuando el registro fue eliminado logicamente.
- El POS debe tratar `deleted_at != null` como una baja remota.
- El POS debe tratar `is_active = false` como desactivacion sin borrado.
- Toda baja logica debe incrementar `sync_version`.

## Idempotencia

- Si un item llega con un `uuid` ya existente, el servidor responde `status = skipped`.
- El POS puede reintentar el mismo batch sin crear duplicados si conserva los `uuid`.
- Para ventas y movimientos, los `uuid` son la llave primaria de idempotencia de negocio.

## Conflictos clasificados

- `duplicate_sale_number`: el `sale_number` ya existe con otro `uuid`.
- `missing_device`: el dispositivo emisor no existe o no esta activo.
- `missing_customer`: la referencia del cliente no existe en el servidor.
- `missing_user`: la referencia del usuario no existe en el servidor.
- `missing_product`: la referencia del producto no existe en el servidor.
- `missing_sale`: la referencia de la venta no existe en el servidor.

## Contrato por item en uploads batch

Cada item procesado debe regresar:

```json
{
  "uuid": "c0a801d0-1111-4f16-9f06-1e4cb755dd4a",
  "status": "created",
  "code": null,
  "message": "Venta sincronizada correctamente.",
  "server_entity": {},
  "errors": []
}
```

Estados soportados:

- `created`: el item fue aceptado y persistido
- `updated`: reservado para futuras reconciliaciones seguras
- `skipped`: el item ya existia y se trato como idempotente
- `conflict`: el item no se acepto por una regla de negocio o referencia rota
- `failed`: ocurrio un error inesperado del servidor

## Fechas de origen y recepcion

- `client_generated_at`: momento en que el POS genero el evento localmente.
- `received_at`: momento en que el servidor recibio el payload.
- Si el POS no manda `received_at`, el servidor lo rellena al momento de procesar.

## Reglas para el POS

- Guardar el `uuid` local de cada venta y movimiento antes de sincronizar.
- Reintentar solo items con `status = failed` o `status = conflict` despues de corregir la causa.
- No volver a crear localmente items con `status = skipped`; ya existen en servidor.
- Si `server_entity` viene presente en un `conflict`, usarlo como referencia canonica.
