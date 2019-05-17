<?php

namespace LaravelFeature\Provider;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use LaravelFeature\Domain\Repository\FeatureRepositoryInterface;
use LaravelFeature\Console\Command\ScanViewsForFeaturesCommand;

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

        $this->registerBladeDirectives();
        $this->registerConsoleCommand();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/features.php', 'features');

        $config = $this->app->make('config');

        $this->app->bind(FeatureRepositoryInterface::class, function () use ($config) {
            return app()->make($config->get('features.repository'));
        });
    }

    private function registerBladeDirectives()
    {
        $this->registerBladeFeatureDirective();
        $this->registerBladeFeatureForDirective();
    }

    private function registerBladeFeatureDirective()
    {
        Blade::directive('feature', function ($featureName) {
            return "<?php if (app(\\LaravelFeature\\Domain\\FeatureManager::class)->isEnabled($featureName)): ?>";
        });

        Blade::directive('endfeature', function () {
            return '<?php endif; ?>';
        });
    }

    private function registerBladeFeatureForDirective()
    {
        Blade::directive('featurefor', function ($args) {
            return "<?php if (app(\\LaravelFeature\\Domain\\FeatureManager::class)->isEnabledFor($args)): ?>";
        });

        Blade::directive('endfeaturefor', function () {
            return '<?php endif; ?>';
        });
    }

    private function registerConsoleCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ScanViewsForFeaturesCommand::class
            ]);
        }
    }
}
