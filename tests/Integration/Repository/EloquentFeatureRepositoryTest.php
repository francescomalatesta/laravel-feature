<?php

namespace LaravelFeature\Tests\Integration\Repository;


use LaravelFeature\Domain\Exception\FeatureException;
use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Repository\EloquentFeatureRepository;
use LaravelFeature\Tests\TestCase;

class EloquentFeatureRepositoryTest extends TestCase
{
    /** @var EloquentFeatureRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new EloquentFeatureRepository();
    }

    public function testSave()
    {
        $feature = Feature::fromNameAndStatus('my.feature', true);

        $this->repository->save($feature);

        $this->seeInDatabase('features', [
            'name' => 'my.feature',
            'is_enabled' => true
        ]);
    }

    /**
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     */
    public function testSaveThrowsExceptionOnError()
    {
        $feature = Feature::fromNameAndStatus(null, true);

        $this->repository->save($feature);
    }

    public function testRemove()
    {
        $this->addTestFeature();

        $feature = Feature::fromNameAndStatus('test.feature', true);

        $this->repository->remove($feature);

        $this->dontSeeInDatabase('features', [
            'name' => 'test.feature'
        ]);
    }

    /**
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     * @expectedExceptionMessage Unable to find the feature.
     */
    public function testRemoveThrowsErrorOnFeatureNotFound()
    {
        $this->addTestFeature();

        $feature = Feature::fromNameAndStatus('unknown.feature', true);

        $this->repository->remove($feature);
    }

    public function testFindByName()
    {
        $this->addTestFeature();

        /** @var Feature $feature */
        $feature = $this->repository->findByName('test.feature');

        $this->assertNotNull($feature);
    }

    /**
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     */
    public function testFindByNameThrowsErrorOnFeatureNotFound()
    {
        $this->addTestFeature();

        /** @var Feature $feature */
        $this->repository->findByName('unknown.feature');
    }

    private function addTestFeature()
    {
        \DB::table('features')->insert([
            'name' => 'test.feature',
            'is_enabled' => true
        ]);
    }
}
