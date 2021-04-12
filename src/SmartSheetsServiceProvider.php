<?php

namespace DgoodGdba\SmartSheets;

use Illuminate\Support\ServiceProvider;

class SmartSheetsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('smart-sheets', SmartSheets::class);
//        $this->app->bind('smart-sheets', function ($app) {
//            return new SmartSheets();
//        });

        $this->mergeConfigFrom(__DIR__ . '/../config/smart-sheets.php', 'smart-sheets');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/smart-sheets.php' => $this->app->configPath('smart-sheets.php'),
        ], 'config');
    }
}
