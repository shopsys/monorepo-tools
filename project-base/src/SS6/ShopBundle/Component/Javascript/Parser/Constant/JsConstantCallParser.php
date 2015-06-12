<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Constant;

import('PLUG.JavaScript.JLexBase'); // contains J_* constants
import('PLUG.JavaScript.JNodes.nonterminal.JCallExprNode');
import('PLUG.JavaScript.JNodes.nonterminal.JProgramNode');

use JCallExprNode;
use JProgramNode;
use SS6\ShopBundle\Component\Javascript\Parser\JsFunctionCallParser;
use SS6\ShopBundle\Component\Javascript\Parser\JsStringParser;

class JsConstantCallParser {

	const FUNCTION_NAME = 'SS6.constant';
	const NAME_ARGUMENT_INDEX = 0;

	/**
	 * @var \SS6\ShopBundle\Component\Javascript\Parser\JsFunctionCallParser
	 */
	private $jsFunctionCallParser;

	/**
	 * @var \SS6\ShopBundle\Component\Javascript\Parser\JsStringParser
	 */
	private $jsStringParser;

	/**
	 * @param \SS6\ShopBundle\Component\Javascript\Parser\JsFunctionCallParser $jsFunctionCallParser
	 * @param \SS6\ShopBundle\Component\Javascript\Parser\JsStringParser $jsStringParser
	 */
	public function __construct(
		JsFunctionCallParser $jsFunctionCallParser,
		JsStringParser $jsStringParser
	) {
		$this->jsFunctionCallParser = $jsFunctionCallParser;
		$this->jsStringParser = $jsStringParser;
	}

	/**
	 * @param \JProgramNode $node
	 * @return \SS6\ShopBundle\Component\Javascript\Parser\Constant\JsConstantCall[]
	 */
	public function parse(JProgramNode $node) {
		$jsConstantCalls = [];

		$callExprNodes = $node->get_nodes_by_symbol(J_CALL_EXPR);
		/* @var $callExprNodes \JCallExprNode[] */
		foreach ($callExprNodes as $callExprNode) {
			if ($this->isConstantFunctionCall($callExprNode)) {
				$constantNameArgumentNode = $this->getConstantNameArgumentNode($callExprNode);
				$constantName = $this->getConstantName($constantNameArgumentNode);

				$jsConstantCalls[] = new JsConstantCall(
					$callExprNode,
					$constantName
				);
			}
		}

		return $jsConstantCalls;
	}

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return bool
	 */
	private function isConstantFunctionCall(JCallExprNode $callExprNode) {
		$functionName = $this->jsFunctionCallParser->getFunctionName($callExprNode);

		return $functionName === self::FUNCTION_NAME;
	}

	/**
	 * @param \JNodeBase $constantNameArgumentNode
	 * @return string
	 */
	private function getConstantName(\JNodeBase $constantNameArgumentNode) {
		try {
			$constantName = $this->jsStringParser->getConcatenatedString($constantNameArgumentNode);
		} catch (\SS6\ShopBundle\Component\Javascript\Parser\Exception\UnsupportedNodeException $ex) {
			throw new \SS6\ShopBundle\Component\Javascript\Parser\Constant\Exception\JsConstantCallParserException(
				'Cannot parse constant name ' . (string)$constantNameArgumentNode
					. ' at line ' . $constantNameArgumentNode->get_line_num()
					. ', column ' . $constantNameArgumentNode->get_col_num(),
				$ex
			);
		}

		return $constantName;
	}

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return \JNodeBase
	 */
	private function getConstantNameArgumentNode(JCallExprNode $callExprNode) {
		$argumentNodes = $this->jsFunctionCallParser->getArgumentNodes($callExprNode);
		if (!isset($argumentNodes[self::NAME_ARGUMENT_INDEX])) {
			throw new \SS6\ShopBundle\Component\Javascript\Parser\Constant\Exception\JsConstantCallParserException(
				'Constant name argument not specified at line ' . $callExprNode->get_line_num()
					. ', column ' . $callExprNode->get_col_num()
			);
		}

		return $argumentNodes[self::NAME_ARGUMENT_INDEX];
	}

}
