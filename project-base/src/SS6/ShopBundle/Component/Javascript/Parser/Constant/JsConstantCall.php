<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Constant;

import('PLUG.JavaScript.JNodes.JNodeBase');
import('PLUG.JavaScript.JNodes.nonterminal.JCallExprNode');

use JCallExprNode;
use JNodeBase;

class JsConstantCall {

	/**
	 * @var \JCallExprNode
	 */
	private $callExprNode;

	/**
	 * @var \JNodeBase
	 */
	private $constantNameArgumentNode;

	/**
	 * @var string
	 */
	private $constantName;

	/**
	 * @param \JCallExprNode $callExprNode
	 * @param \JNodeBase $constantNameArgumentNode
	 * @param string $constantName
	 */
	public function __construct(
		JCallExprNode $callExprNode,
		JNodeBase $constantNameArgumentNode,
		$constantName
	) {
		$this->callExprNode = $callExprNode;
		$this->constantNameArgumentNode = $constantNameArgumentNode;
		$this->constantName = $constantName;
	}

	/**
	 * @return \JCallExprNode
	 */
	public function getCallExprNode() {
		return $this->callExprNode;
	}

	/**
	 * @return \JNodeBase
	 */
	public function getConstantNameArgumentNode() {
		return $this->constantNameArgumentNode;
	}

	/**
	 * @return string
	 */
	public function getConstantName() {
		return $this->constantName;
	}

}
