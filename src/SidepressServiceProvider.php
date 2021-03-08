<?php

namespace Avexsoft\Sidepress;

use Illuminate\Support\ServiceProvider;

class SidepressServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'avexsoft');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'avexsoft');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sidepress.php', 'sidepress');

        // Register the service the package provides.
        $this->app->singleton('sidepress', function ($app) {
            return new Sidepress;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sidepress'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/sidepress.php' => config_path('sidepress.php'),
        ], 'sidepress.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/avexsoft'),
        ], 'sidepress.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/avexsoft'),
        ], 'sidepress.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/avexsoft'),
        ], 'sidepress.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
