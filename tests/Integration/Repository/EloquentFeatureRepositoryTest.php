<?php

namespace LaravelFeature\Tests\Integration\Repository;


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

    public function testRemove()
    {
        $this->addTestFeature();

        $feature = Feature::fromNameAndStatus('test.feature', true);

        $this->repository->remove($feature);

        $this->dontSeeInDatabase('features', [
            'name' => 'test.feature'
        ]);
    }

    public function testFindByName()
    {
        $this->addTestFeature();

        /** @var Feature $feature */
        $feature = $this->repository->findByName('test.feature');

        $this->assertTrue($feature->isEnabled());
    }

    private function addTestFeature()
    {
        \DB::table('features')->insert([
            'name' => 'test.feature',
            'is_enabled' => true
        ]);
    }
}
