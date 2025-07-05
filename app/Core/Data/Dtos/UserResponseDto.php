<?php

namespace App\Core\Data\Dtos;

use Spatie\LaravelData\Data;

class UserResponseDTO extends Data
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $fullName,
        public string $email,
        public string $phone,
        public string $role,
        public bool $isActive,
        public string $createdAt,
    ) {}
}
