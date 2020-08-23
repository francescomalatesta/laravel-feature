<?php

namespace LaravelFeature\Tests\Integration\Service;

use LaravelFeature\Tests\TestCase;
use LaravelFeature\Domain\FeatureManager;
use LaravelFeature\Service\FeaturesViewScanner;

class FeaturesViewScannerTest extends TestCase
{
    /** @var FeaturesViewScanner */
    private $service;

    public function setUp() : void
    {
        parent::setUp();

        /** @var FeatureManager $managerMock */
        $managerMock = $this->getMockBuilder(FeatureManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new FeaturesViewScanner($managerMock, app()->make('config'));
    }

    /**
     * Tests the service is able to find features.
     */
    public function testServiceFindsFeaturesRight()
    {
        $foundDirectives = $this->service->scan();

        $this->assertCount(4, $foundDirectives);
        $this->assertEquals([
            'my.feature',
            'my.second_feature',
            'my.feature.for',
            'my.second_feature.for'
        ], $foundDirectives);
    }
}
