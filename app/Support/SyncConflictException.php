<?php

namespace App\Support;

use RuntimeException;

class SyncConflictException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly array $errors = [],
        private readonly ?string $conflictCode = null,
    ) {
        parent::__construct($message);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function conflictCode(): ?string
    {
        return $this->conflictCode;
    }
}
