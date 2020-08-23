<?php

namespace LaravelFeature\Service;

use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use LaravelFeature\Domain\FeatureManager;

class FeaturesViewScanner
{
    /** @var FeatureManager */
    private $featureManager;

    /** @var Repository */
    private $config;

    /**
     * FeaturesViewScanner constructor.
     *
     * @param FeatureManager $featureManager
     * @param Repository $config
     */
    public function __construct(FeatureManager $featureManager, Repository $config)
    {
        $this->featureManager = $featureManager;
        $this->config = $config;
    }

    public function scan()
    {
        $pathsToBeScanned = $this->config->get('features.scanned_paths', [ 'resources/views' ]);

        $foundDirectives = [];

        foreach ($pathsToBeScanned as $path) {
            $views = $this->getAllBladeViewsInPath($path);

            foreach ($views as $view) {
                $foundDirectives = array_merge($foundDirectives, $this->getFeaturesForView($view));
            }
        }

        $foundDirectives = array_unique($foundDirectives);

        foreach ($foundDirectives as $directive) {
            $this->featureManager->add($directive, $this->config->get('features.scanned_default_enabled'));
        }

        return $foundDirectives;
    }

    private function getAllBladeViewsInPath($path)
    {
        $files = scandir($path);
        $files = array_diff($files, ['..', '.']);

        $bladeViews = [];

        foreach ($files as $file) {
            $itemPath = $path . DIRECTORY_SEPARATOR . $file;

            if (is_dir($itemPath)) {
                $bladeViews = array_merge($bladeViews, $this->getAllBladeViewsInPath($itemPath));
            }

            if (is_file($itemPath) && Str::endsWith($file, '.blade.php')) {
                $bladeViews[] = $itemPath;
            }
        }

        return $bladeViews;
    }

    private function getFeaturesForView($view)
    {
        $fileContents = file_get_contents($view);

        preg_match_all('/@feature\(["\'](.+)["\']\)|@featurefor\(["\'](.+)["\']\,.*\)/', $fileContents, $results);

        return collect($results[1])
            ->merge($results[2])
            ->filter()
            ->toArray();
    }
}
