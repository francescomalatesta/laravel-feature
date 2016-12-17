<?php

namespace LaravelFeature\Domain;


use LaravelFeature\Domain\Model\Feature;

class FeatureManager
{
    /** @var FeatureRepositoryInterface */
    private $repository;

    /**
     * FeatureManager constructor.
     * @param FeatureRepositoryInterface $repository
     */
    public function __construct(FeatureRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function add($featureName, $isEnabled)
    {
        $feature = Feature::fromNameAndStatus($featureName, $isEnabled);
        $this->repository->save($feature);
    }

    public function remove($featureName)
    {
        $feature = $this->repository->findByName($featureName);
        $this->repository->remove($feature);
    }

    public function rename($featureOldName, $featureNewName)
    {
        /** @var Feature $feature */
        $feature = $this->repository->findByName($featureOldName);
        $feature->setNewName($featureNewName);

        $this->repository->save($feature);
    }

    public function enable($featureName)
    {
        /** @var Feature $feature */
        $feature = $this->repository->findByName($featureName);

        $feature->enable();

        $this->repository->save($feature);
    }

    public function disable($featureName)
    {
        /** @var Feature $feature */
        $feature = $this->repository->findByName($featureName);

        $feature->disable();

        $this->repository->save($feature);
    }

    public function isEnabled($featureName)
    {
        /** @var Feature $feature */
        $feature = $this->repository->findByName($featureName);
        return $feature->isEnabled();
    }
}
