<?php

namespace App\Services\Sync;

class SyncAuthorityService
{
    public const DOMAIN_CATALOG = 'catalog';
    public const DOMAIN_SALES_UPLOAD = 'sales_upload';
    public const DOMAIN_INVENTORY_MOVEMENTS_UPLOAD = 'inventory_movements_upload';
    public const DOMAIN_CASH_CLOSURES_UPLOAD = 'cash_closures_upload';
    public const DOMAIN_CUSTOMERS = 'customers';

    public function forCatalog(): array
    {
        return [
            'domain' => self::DOMAIN_CATALOG,
            'winner' => 'server',
            'rule' => 'server_catalog_is_authoritative',
        ];
    }

    public function forSalesUpload(): array
    {
        return [
            'domain' => self::DOMAIN_SALES_UPLOAD,
            'winner' => 'pos',
            'rule' => 'pos_sale_is_accepted_when_uuid_is_missing_on_server',
        ];
    }

    public function forInventoryMovementsUpload(): array
    {
        return [
            'domain' => self::DOMAIN_INVENTORY_MOVEMENTS_UPLOAD,
            'winner' => 'pos',
            'rule' => 'pos_inventory_movement_is_accepted_when_uuid_is_missing_on_server',
        ];
    }

    public function forCashClosuresUpload(): array
    {
        return [
            'domain' => self::DOMAIN_CASH_CLOSURES_UPLOAD,
            'winner' => 'pos',
            'rule' => 'pos_cash_closure_is_accepted_when_uuid_is_missing_on_server',
        ];
    }

    public function forCustomers(): array
    {
        return [
            'domain' => self::DOMAIN_CUSTOMERS,
            'winner' => 'server',
            'rule' => 'server_customer_data_is_authoritative_after_registration; pos_only_creates_when_uuid_is_missing',
        ];
    }
}
