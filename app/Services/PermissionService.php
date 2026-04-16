<?php

namespace App\Services;

use App\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $repo
    ) {
    }

    public function paginate(array $filters)
    {
        return $this->repo->paginate($filters);
    }
}