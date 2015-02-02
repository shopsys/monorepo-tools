<?php

namespace SS6\ShopBundle\Component\Javascript\Compiler\Constant;

import('PLUG.JavaScript.JNodes.nonterminal.JProgramNode');

use JProgramNode;
use SS6\ShopBundle\Component\Javascript\Compiler\JsCompilerPassInterface;
use SS6\ShopBundle\Component\Javascript\Parser\Constant\JsConstantCallParser;

class JsConstantCompilerPass implements JsCompilerPassInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Javascript\Parser\Constant\JsConstantCallParser
	 */
	private $jsConstantCallParser;

	public function __construct(
		JsConstantCallParser $jsConstantCallParser
	) {
		$this->jsConstantCallParser = $jsConstantCallParser;
	}

	/**
	 * @param \JProgramNode $node
	 */
	public function process(JProgramNode $node) {
		$jsConstantCalls = $this->jsConstantCallParser->parse($node);

		foreach ($jsConstantCalls as $jsConstantCall) {
			$constantNameArgumentNode = $jsConstantCall->getConstantNameArgumentNode();
			$constantName = $jsConstantCall->getConstantName();

			if (!defined($constantName)) {
				throw new \SS6\ShopBundle\Component\Javascript\Compiler\Constant\Exception\ConstantNotFoundException(
					'Constant ' . $constantName . ' not defined in PHP code'
				);
			}

			$constantValue = constant($jsConstantCall->getConstantName());
			$constantValueJson = json_encode($constantValue);

			if ($constantValueJson === false) {
				throw new \SS6\ShopBundle\Component\Javascript\Compiler\Constant\Exception\CannotConvertToJsonException(
					'Constant ' . $constantName . ' cannot be converted to JSON'
				);
			}

			$constantNameArgumentNode->terminate(json_encode($constantValue));
		}
	}

}
