<?php

namespace Tests\ShopBundle\Unit\Javascript\Compiler\Constant;

use Shopsys\ShopBundle\Component\Javascript\Compiler\Constant\JsConstantCompilerPass;
use Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompiler;
use Tests\ShopBundle\Test\FunctionalTestCase;

class JsConstantCompilerPassTest extends FunctionalTestCase
{
    public function testProcess()
    {
        $jsConstantCompilerPass = $this->getContainer()->get(JsConstantCompilerPass::class);

        $jsCompiler = new JsCompiler([
            $jsConstantCompilerPass,
        ]);

        $content = file_get_contents(__DIR__ . '/testDefinedConstant.js');
        $result = $jsCompiler->compile($content);

        $expectedResult = <<<EOD
var x = "bar";
var y = "bar2";
EOD;

        $this->assertSame($expectedResult, $result);
    }

    public function testProcessConstantNotFoundException()
    {
        $this->setExpectedException(\Shopsys\ShopBundle\Component\Javascript\Compiler\Constant\Exception\ConstantNotFoundException::class);

        $jsConstantCompilerPass = $this->getContainer()->get(JsConstantCompilerPass::class);

        $jsCompiler = new JsCompiler([
            $jsConstantCompilerPass,
        ]);

        $content = file_get_contents(__DIR__ . '/testUndefinedConstant.js');
        $jsCompiler->compile($content);
    }
}
