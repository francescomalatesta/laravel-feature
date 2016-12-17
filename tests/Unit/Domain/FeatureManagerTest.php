<?php

namespace LaravelFeature\Tests\Domain;


use LaravelFeature\Domain\FeatureManager;
use LaravelFeature\Domain\Exception\FeatureException;
use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Domain\Repository\FeatureRepositoryInterface;

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
            ->setMethods(['save', 'remove', 'findByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = new FeatureManager($this->repositoryMock);
    }

    public function testAdd()
    {
        $this->repositoryMock->expects($this->once())
            ->method('save');

        $this->manager->add('my.feature', true);
    }

    /**
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
}
