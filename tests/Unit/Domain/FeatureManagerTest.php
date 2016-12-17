<?php

namespace LaravelFeature\Tests\Domain;


use LaravelFeature\Domain\FeatureManager;
use LaravelFeature\Domain\Exception\FeatureException;
use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Domain\Repository\FeatureRepositoryInterface;
use LaravelFeature\Featurable\FeaturableInterface;


class FeatureManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FeatureRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $repositoryMock;

    /** @var FeatureManager */
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $this->repositoryMock = $this->getMockBuilder(FeatureRepositoryInterface::class)
            ->setMethods(['save', 'remove', 'findByName', 'enableFor', 'disableFor', 'isEnabledFor'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = new FeatureManager($this->repositoryMock);
    }

    /**
     * Tests that everything goes well when adding a new feature to the system.
     */
    public function testAdd()
    {
        $this->repositoryMock->expects($this->once())
            ->method('save');

        $this->manager->add('my.feature', true);
    }

    /**
     * Tests an exception is thrown if something goes wrong during the saving of a new feature.
     *
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     * @expectedExceptionMessage Unable to save the feature.
     */
    public function testAddThrowsExceptionOnError()
    {
        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException(new FeatureException('Unable to save the feature.'));

        $this->manager->add('my.feature', true);
    }

    /**
     * Tests that everything goes well during a feature removal.
     */
    public function testRemove()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->repositoryMock->expects($this->once())
            ->method('remove');

        $this->manager->remove('my.feature');
    }

    /**
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     * @expectedExceptionMessage Unable to remove the feature.
     */
    public function testRemoveThrowsExceptionOnError()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->repositoryMock->expects($this->once())
            ->method('remove')
            ->willThrowException(new FeatureException('Unable to remove the feature.'));

        $this->manager->remove('my.feature');
    }

    /**
     * Tests that everything goes well during a feature rename.
     */
    public function testRenameFeature()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->repositoryMock->expects($this->once())
            ->method('save');

        $this->manager->rename('old.name', 'new.name');
    }

    /**
     * Tests that an exception is thrown if the feature is not found.
     *
     * @expectedException \LaravelFeature\Domain\Exception\FeatureException
     * @expectedExceptionMessage Unable to save the feature.
     */
    public function testRenameFeatureThrowsError()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException(new FeatureException('Unable to save the feature.'));

        $this->manager->rename('old.feature', 'new.feature');
    }

    /**
     * Tests everything goes well when a feature is globally enabled.
     */
    public function testEnableFeature()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature->expects($this->once())
            ->method('enable');

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->repositoryMock->expects($this->once())
            ->method('save');

        $this->manager->enable('my.feature');
    }

    /**
     * Tests everything goes well when a feature is globally disabled.
     */
    public function testDisableFeature()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature->expects($this->once())
            ->method('disable');

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->repositoryMock->expects($this->once())
            ->method('save');

        $this->manager->disable('my.feature');
    }

    /**
     * Tests the manager can correctly check if a feature is enabled or not.
     */
    public function testFeatureIsEnabled()
    {
        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->repositoryMock->expects($this->once())
            ->method('findByName')
            ->willReturn($feature);

        $this->assertTrue($this->manager->isEnabled('my.feature'));
    }

    public function testEnableFor()
    {
        /** @var FeaturableInterface | \PHPUnit_Framework_MockObject_MockObject $featurableMock */
        $featurableMock = $this->getMockBuilder(FeaturableInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('enableFor');

        $this->manager->enableFor('my.feature', $featurableMock);
    }

    public function testDisableFor()
    {
        /** @var FeaturableInterface | \PHPUnit_Framework_MockObject_MockObject $featurableMock */
        $featurableMock = $this->getMockBuilder(FeaturableInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('disableFor');

        $this->manager->disableFor('my.feature', $featurableMock);
    }

    public function testIsEnabledFor()
    {
        /** @var FeaturableInterface | \PHPUnit_Framework_MockObject_MockObject $featurableMock */
        $featurableMock = $this->getMockBuilder(FeaturableInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('isEnabledFor');

        $this->manager->isEnabledFor('my.feature', $featurableMock);
    }
}
