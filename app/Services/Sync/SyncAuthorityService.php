<?php

namespace App\Services\Sync;

class SyncAuthorityService
{
    public const DOMAIN_CATALOG = 'catalog';
    public const DOMAIN_SALES_UPLOAD = 'sales_upload';

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
}
