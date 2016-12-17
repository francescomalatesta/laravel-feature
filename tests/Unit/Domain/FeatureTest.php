<?php

namespace LaravelFeature\Tests\Domain;


use LaravelFeature\Domain\Model\Feature;

class FeatureTest extends \PHPUnit_Framework_TestCase
{
    public function testFeatureCreation()
    {
        $feature = Feature::fromNameAndStatus('my.feature', false);

        $this->assertEquals('my.feature', $feature->getName());
        $this->assertFalse($feature->isEnabled());
    }

    public function testNameChange()
    {
        $feature = Feature::fromNameAndStatus('old.name', false);
        $feature->setNewName('new.name');

        $this->assertEquals('new.name', $feature->getName());
    }

    public function testEnable()
    {
        $feature = Feature::fromNameAndStatus('my.feature', false);

        $feature->enable();

        $this->assertTrue($feature->isEnabled());
    }

    public function testDisable()
    {
        $feature = Feature::fromNameAndStatus('my.feature', true);

        $feature->disable();

        $this->assertFalse($feature->isEnabled());
    }
}
