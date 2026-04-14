<?php

namespace App\Providers;

use App\Repositories\Interfaces\PositionRepositoryInterface;
use App\Repositories\PositionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
    }
}