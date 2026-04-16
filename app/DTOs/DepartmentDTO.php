<?php

namespace App\DTOs;

class DepartmentDto
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}