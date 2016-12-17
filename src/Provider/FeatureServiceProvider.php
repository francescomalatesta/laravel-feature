<?php

namespace LaravelFeature\Provider;

use Illuminate\Support\ServiceProvider;
use LaravelFeature\Repository\EloquentFeatureRepository;
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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FeatureRepositoryInterface::class, function () {
            return new EloquentFeatureRepository();
        });
    }
}
