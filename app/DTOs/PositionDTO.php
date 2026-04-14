<?php

namespace App\DTOs;

class PositionDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $department_id,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'department_id' => $this->department_id,
        ];
    }
}