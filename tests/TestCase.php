<?php

namespace LaravelFeature\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../src/Migration');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('features.scanned_paths', [ __DIR__ . '/Integration/Service/test_folder' ]);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [\LaravelFeature\Provider\FeatureServiceProvider::class];
    }
}
