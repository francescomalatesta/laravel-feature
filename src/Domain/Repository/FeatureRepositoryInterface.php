<?php

namespace LaravelFeature\Domain;


use LaravelFeature\Domain\Model\Feature;

interface FeatureRepositoryInterface
{
    public function save(Feature $feature);

    public function remove(Feature $feature);

    public function findByName($featureName);
}
