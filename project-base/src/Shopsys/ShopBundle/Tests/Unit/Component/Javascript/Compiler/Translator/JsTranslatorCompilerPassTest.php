<?php

namespace Shopsys\ShopBundle\Tests\Unit\Javascript\Compiler\Translator;

use Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompiler;
use Shopsys\ShopBundle\Component\Javascript\Compiler\Translator\JsTranslatorCompilerPass;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;

class JsTranslatorCompilerPassTest extends FunctionalTestCase {

    public function testProcess() {
        $translator = $this->getContainer()->get('translator');
        /* @var $translator \Shopsys\ShopBundle\Component\Translation\Translator */
        $jsTranslatorCompilerPass = $this->getContainer()->get(JsTranslatorCompilerPass::class);
        /* @var $jsTranslatorCompilerPass \Shopsys\ShopBundle\Component\Javascript\Compiler\Translator\JsTranslatorCompilerPass */

        // set undefined locale to make Translator add '##' prefix
        $translator->setLocale('undefinedLocale');

        $jsCompiler = new JsCompiler([
            $jsTranslatorCompilerPass,
        ]);

        $content = file_get_contents(__DIR__ . '/testFoo.js');
        $result = $jsCompiler->compile($content);

        $expectedResult = <<<EOD
var x = Shopsys.translator.trans ( "##foo" );
var y = Shopsys.translator.trans ( "##foo2", { 'param' : 'value' }, 'asdf' );
var z = Shopsys.translator.transChoice ( "##foo3" );
EOD;

        $this->assertSame($expectedResult, $result);
    }

}
