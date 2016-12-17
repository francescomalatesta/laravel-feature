<?php

namespace LaravelFeature\Tests\Integration\Repository;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
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

    public function testEnableFor()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature();

        $this->repository->enableFor('test.feature', $entity);

        $this->seeInDatabase('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    /**
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     */
    public function testEnableForThrowsErrorOnFeatureNotFound()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $this->addTestFeature();

        $this->repository->enableFor('unknown.feature', $entity);

        $this->dropTestEntityTable();
    }

    public function testEnableForDoesNothingIfFeatureIsGloballyEnabled()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature('test.feature', true);

        $this->repository->enableFor('test.feature', $entity);

        $this->dontSeeInDatabase('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    public function testDisableFor()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature();
        $this->enableTestFeatureOn($entity);

        $this->repository->disableFor('test.feature', $entity);

        $this->dontSeeInDatabase('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

    /**
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     */
    public function testDisableForThrowsErrorOnFeatureNotFound()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $this->addTestFeature();
        $this->enableTestFeatureOn($entity);

        $this->repository->disableFor('unknown.feature', $entity);

        $this->dropTestEntityTable();
    }

    public function testDisableForDoesNothingIfFeatureIsGloballyEnabled()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();
        $feature = $this->addTestFeature('test.feature', true);

        $this->repository->disableFor('test.feature', $entity);

        $this->dontSeeInDatabase('featurables', [
            'feature_id' => $feature->id,
            'featurable_id' => $entity->id,
            'featurable_type' => get_class($entity)
        ]);

        $this->dropTestEntityTable();
    }

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
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     * @expectedExceptionMessage Unable to find the feature.
     */
    public function testIsEnabledForThrowsExceptionOnFeatureNotFound()
    {
        $this->createTestEntityTable();

        $entity = $this->addTestEntity();

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
        \Schema::create('featurabletestentities', function(Blueprint $table){
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
