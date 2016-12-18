<?php

namespace LaravelFeature\Console\Command;

use Illuminate\Console\Command;
use LaravelFeature\Service\FeaturesViewScanner;

class ScanViewsForFeaturesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feature:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan project views to find new features.';

    /**
     * @var FeaturesViewScanner
     */
    private $service;


    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->service = app()->make(FeaturesViewScanner::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $features = $this->service->scan();
        $areEnabledByDefault = config('features.scanned_default_enabled');

        $this->getOutput()->writeln('');

        if (count($features) === 0) {
            $this->error('No features were found in the project views!');
            $this->getOutput()->writeln('');
            return;
        }

        $this->info(count($features) . ' features found in views:');
        $this->getOutput()->writeln('');

        foreach ($features as $feature) {
            $this->getOutput()->writeln('- ' . $feature);
        }

        $this->getOutput()->writeln('');
        $this->info('All the new features were added to the database with the '
            . ($areEnabledByDefault ? 'ENABLED' : 'disabled') .
            ' status by default. Nothing changed for the already present ones.');

        $this->getOutput()->writeln('');
    }
}
