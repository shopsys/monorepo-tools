<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Constant;

import('PLUG.JavaScript.JNodes.nonterminal.JCallExprNode');

use JCallExprNode;

class JsConstantCall {

	/**
	 * @var \JCallExprNode
	 */
	private $callExprNode;

	/**
	 * @var string
	 */
	private $constantName;

	/**
	 * @param \JCallExprNode $callExprNode
	 * @param string $constantName
	 */
	public function __construct(
		JCallExprNode $callExprNode,
		$constantName
	) {
		$this->callExprNode = $callExprNode;
		$this->constantName = $constantName;
	}

	/**
	 * @return \JCallExprNode
	 */
	public function getCallExprNode() {
		return $this->callExprNode;
	}

	/**
	 * @return string
	 */
	public function getConstantName() {
		return $this->constantName;
	}

}
