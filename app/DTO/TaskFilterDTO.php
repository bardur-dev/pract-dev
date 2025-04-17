<?php

namespace App\DTO;

class TaskFilterDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?string $status
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null
        );
    }
}
