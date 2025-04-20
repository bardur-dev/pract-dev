<?php

namespace App\DTO;

class TaskFilterDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?string $status,
        public ?int $user_id = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            user_id: $data['user_id'] ?? null
        );
    }
}
