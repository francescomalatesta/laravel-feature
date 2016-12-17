<?php

namespace LaravelFeature\Facade;

use LaravelFeature\Domain\FeatureManager;
use Illuminate\Support\Facades\Facade;

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
