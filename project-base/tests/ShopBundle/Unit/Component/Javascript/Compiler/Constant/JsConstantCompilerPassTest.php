<?php

namespace Tests\ShopBundle\Unit\Javascript\Compiler\Constant;

use Shopsys\ShopBundle\Component\Javascript\Compiler\Constant\JsConstantCompilerPass;
use Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompiler;
use Tests\ShopBundle\Test\FunctionalTestCase;

class JsConstantCompilerPassTest extends FunctionalTestCase
{
    public function testJsCompilerReplacesDefinedConstants()
    {
        $content = file_get_contents(__DIR__ . '/testDefinedConstant.js');
        $result = $this->getJsCompiler()->compile($content);

        $expectedResult = file_get_contents(__DIR__ . '/testDefinedConstant.expected.js');

        $this->assertSame($expectedResult, $result);
    }

    public function testJsCompilerReplacesClassNames()
    {
        $content = file_get_contents(__DIR__ . '/testClassName.js');
        $result = $this->getJsCompiler()->compile($content);

        $expectedResult = file_get_contents(__DIR__ . '/testClassName.expected.js');

        $this->assertSame($expectedResult, $result);
    }

    public function testJsCompilerFailsOnUndefinedConstant()
    {
        $content = file_get_contents(__DIR__ . '/testUndefinedConstant.js');

        $this->setExpectedException(\Shopsys\ShopBundle\Component\Javascript\Compiler\Constant\Exception\ConstantNotFoundException::class);
        $this->getJsCompiler()->compile($content);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompiler
     */
    private function getJsCompiler()
    {
        $jsConstantCompilerPass = $this->getContainer()->get(JsConstantCompilerPass::class);

        return new JsCompiler([
            $jsConstantCompilerPass,
        ]);
    }
}
