<?php

namespace LaravelFeature\Repository;


use LaravelFeature\Domain\Exception\FeatureException;
use LaravelFeature\Domain\Repository\FeatureRepositoryInterface;
use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Model\Feature as Model;

class EloquentFeatureRepository implements FeatureRepositoryInterface
{
    public function save(Feature $feature)
    {
        /** @var Model $model */
        $model = Model::where('name', '=', $feature->getName())->first();

        if(!$model) {
            $model = new Model();
        }

        $model->name = $feature->getName();
        $model->is_enabled = $feature->isEnabled();

        if(!$model->save()){
            throw new FeatureException('Unable to save the feature.');
        }

        $model->save();
    }

    public function remove(Feature $feature)
    {
        /** @var Model $model */
        $model = Model::where('name', '=', $feature->getName())->first();
        if(!$model) {
            throw new FeatureException('Unable to find the feature.');
        }

        if(!$model->delete()) {
            throw new FeatureException('Unable to remove the feature.');
        }
    }

    public function findByName($featureName)
    {
        /** @var Model $model */
        $model = Model::where('name', '=', $featureName)->first();
        if(!$model) {
            throw new FeatureException('Unable to find the feature.');
        }

        return Feature::fromNameAndStatus(
            $model->name,
            $model->is_enabled
        );
    }
}
