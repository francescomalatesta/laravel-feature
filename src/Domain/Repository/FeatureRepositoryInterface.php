<?php

namespace LaravelFeature\Domain\Repository;

use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Featurable\FeaturableInterface;

interface FeatureRepositoryInterface
{
    public function save(Feature $feature);

    public function remove(Feature $feature);

    public function findByName($featureName);

    public function enableFor($featureName, FeaturableInterface $featurable);

    public function disableFor($featureName, FeaturableInterface $featurable);

    public function isEnabledFor($featureName, FeaturableInterface $featurable);
}
