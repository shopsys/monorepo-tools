<?php

namespace SS6\ShopBundle\Component\Javascript\Compiler;

import('PLUG.JavaScript.JParser');
import('PLUG.JavaScript.JTokenizer');

use JParser;
use JTokenizer;

class JsCompiler {

	/**
	 * @var \SS6\ShopBundle\Component\Javascript\Compiler\JsCompilerPassInterface[]
	 */
	private $compilerPasses;

	/**
	 * @param \SS6\ShopBundle\Component\Javascript\Compiler\JsCompilerPassInterface[] $compilerPasses
	 */
	public function __construct(array $compilerPasses) {
		$this->compilerPasses = $compilerPasses;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function compile($content) {
		$node = JParser::parse_string($content, true, JParser::class, JTokenizer::class);

		foreach ($this->compilerPasses as $compilerPass) {
			$compilerPass->process($node);
		}

		return $node->format();
	}

}
