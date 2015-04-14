<?php

namespace SS6\ShopBundle\Tests\Unit\Javascript\Compiler\Constant;

use SS6\ShopBundle\Component\Javascript\Compiler\JsCompiler;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class JsConstantCompilerPassTest extends FunctionalTestCase {

	public function testProcess() {
		$jsConstantCompilerPass = $this->getContainer()
			->get('ss6.shop.component.javascript.compiler.constant.js_constant_compiler_pass');

		$jsCompiler = new JsCompiler([
			$jsConstantCompilerPass,
		]);

		$content = file_get_contents(__DIR__ . '/testFoo.js');
		$result = $jsCompiler->compile($content);

		$expectedResult = <<<EOD
var x = "bar";
var y = "bar2";
EOD;

		$this->assertSame($expectedResult, $result);
	}

	public function testProcessConstantNotFoundException() {
		$this->setExpectedException(\SS6\ShopBundle\Component\Javascript\Compiler\Constant\Exception\ConstantNotFoundException::class);

		$jsConstantCompilerPass = $this->getContainer()
			->get('ss6.shop.component.javascript.compiler.constant.js_constant_compiler_pass');

		$jsCompiler = new JsCompiler([
			$jsConstantCompilerPass,
		]);

		$content = file_get_contents(__DIR__ . '/testBar.js');
		$jsCompiler->compile($content);
	}

}
