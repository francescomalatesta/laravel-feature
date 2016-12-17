<?php

namespace LaravelFeature\Provider;

use Illuminate\Support\ServiceProvider;
use LaravelFeature\Domain\Repository\FeatureRepositoryInterface;

class FeatureServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Migration');

        $this->publishes([
            __DIR__.'/../Config/features.php' => config_path('features.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/features.php', 'features'
        );

        $config = $this->app->make('config');

        $this->app->bind(FeatureRepositoryInterface::class, function () use ($config) {
            return app()->make($config->get('features.repository'));
        });
    }
}
