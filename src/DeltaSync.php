<?php

namespace Skrypt\DeltaSync;

use Illuminate\Support\ServiceProvider;

class DeltaSync extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/deltasync.php' => config_path('deltasync.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_model_events_table.stub.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_model_events_table.php'),
                __DIR__ . '/../database/migrations/create_model_updates_table.stub.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_model_updates_table.php'),
            ], 'migrations');
        }
    }
}
