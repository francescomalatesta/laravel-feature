<?php

namespace LaravelFeature\Tests\Integration\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use LaravelFeature\Domain\Exception\FeatureException;
use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Featurable\Featurable;
use LaravelFeature\Featurable\FeaturableInterface;
use LaravelFeature\Repository\EloquentFeatureRepository;
use LaravelFeature\Tests\TestCase;
use LaravelFeature\Model\Feature as FeatureModel;

class EloquentFeatureRepositoryTest extends TestCase
{
    /** @var EloquentFeatureRepository */
    private $repository;

    public function setUp() : void
    {
        parent::setUp();

        $this->repository = new EloquentFeatureRepository();
    }

    /**
     * Tests the repository save operation.
     */
    public function testSave()
    {
        $feature = Feature::fromNameAndStatus('my.feature', true);

        $this->repository->save($feature);

        $this->assertDatabaseHas('features', [
            'name' => 'my.feature',
            'is_enabled' => true
        ]);
    }

    /**
     * Tests that the save operation throws an exception if something goes wrong.
     */
    public function testSaveThrowsExceptionOnError()
    {
        $feature = Feature::fromNameAndStatus(null, true);

        $this->expectException(FeatureException::class);

        $this->repository->save($feature);
    }

    /**
     * Tests that the removal operation goes well.
     */
    public function testRemove()
    {
        $this->addTestFeature();

        $feature = Feature::fromNameAndStatus('test.feature', true);

        $this->repository->remove($feature);

        $this->assertDatabaseMissing('features', [
            'name' => 'test.feature'
        ]);
    }

    /**
     * Tests the removal operation throws an exception if the feature is not found.
     */
    public function testRemoveThrowsErrorOnFeatureNotFound()
    {
        $this->addTestFeature();

        $feature = Feature::fromNameAndStatus('unknown.feature', true);

        $this->expectException(FeatureException::class);
        $this->expectExceptionMessage('Unable to find the feature.');

        $this->repository->remove($feature);
    }

    /**
     * Tests a feature is found.
     */
    public function testFindByName()
    {
        $this->addTestFeature();

        /** @var Feature $feature */
        $feature = $this->repository->findByName('test.feature');

        $this->assertNotNull($feature);
    }

    /**
     * Tests an exception is thrown if the feature is not found.
     */
    public function testFindByNameThrowsErrorOnFeatureNotFound()
    {
        $this->addTestFeature();

        $this->expectException(FeatureException::class);

        /** @var Feature $feature */
        $this->repository->findByName('unknown.feature');
    }

    /**
     * Tests the enable operation for a specific FeaturableInterface entity.
     */
    public function testEnableFor()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature();

        $this->repository->enableFor('test.feature', $entity);

        $this->assertDatabaseHas('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    /**
     * Tests the enable operation throws an error if the feature is not found.
     */
    public function testEnableForThrowsErrorOnFeatureNotFound()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $this->addTestFeature();

        $this->expectException(FeatureException::class);

        $this->repository->enableFor('unknown.feature', $entity);

        $this->dropTestEntityTable();
    }

    /**
     * Tests nothing happens if the feature is already enabled globally.
     */
    public function testEnableForDoesNothingIfFeatureIsGloballyEnabled()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature('test.feature', true);

        $this->repository->enableFor('test.feature', $entity);

        $this->assertDatabaseMissing('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    /**
     * Tests the disable of a feature for a specific FeaturableInterface entity.
     */
    public function testDisableFor()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature();
        $this->enableTestFeatureOn($entity);

        $this->repository->disableFor('test.feature', $entity);

        $this->assertDatabaseMissing('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    /**
     * Tests the disable operation throws an error if the feature is not found.
     */
    public function testDisableForThrowsErrorOnFeatureNotFound()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $this->addTestFeature();
        $this->enableTestFeatureOn($entity);

        $this->expectException(FeatureException::class);

        $this->repository->disableFor('unknown.feature', $entity);

        $this->dropTestEntityTable();
    }

    /**
     * Tests nothing happens if the feature is already enabled globally.
     */
    public function testDisableForDoesNothingIfFeatureIsGloballyEnabled()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature('test.feature', true);

        $this->repository->disableFor('test.feature', $entity);

        $this->assertDatabaseMissing('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    /**
     * Tests the enable status of a feature for a specific FeaturableInterface entity.
     */
    public function testIsEnabledFor()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $this->addTestFeature();
        $this->addTestFeature('second.feature');
        $this->enableTestFeatureOn($entity);

        $this->assertTrue($this->repository->isEnabledFor('test.feature', $entity));
        $this->assertFalse($this->repository->isEnabledFor('second.feature', $entity));

        $this->dropTestEntityTable();
    }

    /**
     * Tests an exception is thrown if the feature is not found.
     */
    public function testIsEnabledForThrowsExceptionOnFeatureNotFound()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();

        $this->expectException(FeatureException::class);
        $this->expectExceptionMessage('Unable to find the feature.');

        $this->assertTrue($this->repository->isEnabledFor('test.feature', $entity));

        $this->dropTestEntityTable();
    }

    private function addTestFeature($name = 'test.feature', $isEnabled = false)
    {
        $feature = new FeatureModel;

        $feature->name = $name;
        $feature->is_enabled = $isEnabled;
        $feature->save();

        return $feature;
    }

    private function addTestEntity()
    {
        $entity = new FeaturableTestEntity();
        $entity->name = 'test-entity';
        $entity->save();

        return $entity;
    }

    private function createTestEntityTable()
    {
        \Schema::create('featurabletestentities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    private function dropTestEntityTable()
    {
        \Schema::drop('featurabletestentities');
    }

    private function enableTestFeatureOn($featurable)
    {
        $feature = \LaravelFeature\Model\Feature::first();
        $featurable->features()->attach($feature->id);
    }
}

class FeaturableTestEntity extends Model implements FeaturableInterface
{
    use Featurable;
    protected $table = 'featurabletestentities';
}
