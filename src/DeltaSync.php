<?php

namespace Skrypt\DeltaSync;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Skrypt\DeltaSync\Controllers\DeltaSyncController;

class DeltaSync extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/deltasync.php' => config_path('deltasync.php'),
        ]);
    }

    public function register()
    {
    }
}
