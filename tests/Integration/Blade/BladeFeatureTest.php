<?php

namespace LaravelFeature\Tests\Integration\Blade;

use Illuminate\View\Compilers\BladeCompiler;
use LaravelFeature\Tests\TestCase;

class BladeFeatureTest extends TestCase
{
    /** @var BladeCompiler */
    private $compiler;

    public function setUp() : void
    {
        parent::setUp();

        $this->compiler = app(BladeCompiler::class);
    }

    public function testFeatureStatementsAreCompiled()
    {
        $string = '@feature(\'feature.name\')
feature enabled
@endfeature';

        $expected = '<?php if (app(\\LaravelFeature\\Domain\\FeatureManager::class)->isEnabled(\'feature.name\')): ?>
feature enabled
<?php endif; ?>';

        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    public function testFeatureForStatementsAreCompiled()
    {
        $string = '@featurefor(\'feature.name\', $user)
feature enabled
@endfeaturefor';

        $expected = '<?php if (app(\\LaravelFeature\\Domain\\FeatureManager::class)->isEnabledFor(\'feature.name\', $user)): ?>
feature enabled
<?php endif; ?>';

        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
