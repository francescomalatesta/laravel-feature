<?php

namespace LaravelFeature\Facade;

use LaravelFeature\Domain\FeatureManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void add($featureName, $isEnabled)
 * @method static void remove($featureName)
 * @method static void rename($featureOldName, $featureNewName)
 * @method static void enable($featureName)
 * @method static void disable($featureName)
 * @method static void isEnabled($featureName)
 * @method static bool enableFor($featureName, \LaravelFeature\Featurable\FeaturableInterface $featurable)
 * @method static void disableFor($featureName, \LaravelFeature\Featurable\FeaturableInterface $featurable)
 * @method static bool isEnabledFor($featureName, \LaravelFeature\Featurable\FeaturableInterface $featurable)
 */
class Feature extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return FeatureManager::class;
    }
}
