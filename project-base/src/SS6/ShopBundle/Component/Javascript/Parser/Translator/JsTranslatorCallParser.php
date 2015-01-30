<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Translator;

import('PLUG.JavaScript.JLexBase'); // contains J_* constants
import('PLUG.parsing.LR.LRParseNode'); // JNodeBase is missing import
import('PLUG.JavaScript.JNodes.JNodeBase');
import('PLUG.JavaScript.JNodes.nonterminal.JCallExprNode');
import('PLUG.JavaScript.JNodes.nonterminal.JProgramNode');

use JCallExprNode;
use JNodeBase;
use JProgramNode;

class JsTranslatorCallParser {

	const DEFAULT_MESSAGE_DOMAIN = 'messages';

	/**
	 * @var \SS6\ShopBundle\Component\Translation\TransMethodSpecification[]
	 */
	private $transMethodSpecifications;

	/**
	 * @param \SS6\ShopBundle\Component\Translation\TransMethodSpecification[] $transMethodSpecifications
	 */
	public function __construct(array $transMethodSpecifications) {
		$this->transMethodSpecifications = [];
		foreach ($transMethodSpecifications as $transMethodSpecification) {
			$methodName = $transMethodSpecification->getMethodName();
			$this->transMethodSpecifications[$methodName] = $transMethodSpecification;
		}
	}

	/**
	 * @param \JProgramNode $node
	 * @return \SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCall[]
	 */
	public function parse(JProgramNode $node) {
		$jsTranslatorCalls = [];

		$callExprNodes = $node->get_nodes_by_symbol(J_CALL_EXPR);
		/* @var $callExprNodes \JCallExprNode[] */
		foreach ($callExprNodes as $callExprNode) {
			if ($this->isTransFunctionCall($callExprNode)) {
				$messageIdArgumentNode = $this->getMessageIdArgumentNode($callExprNode);

				$messageId = $this->getMessageId($messageIdArgumentNode);
				$domain = $this->getDomain($callExprNode);

				$jsTranslatorCalls[] = new JsTranslatorCall(
					$callExprNode,
					$messageIdArgumentNode,
					$messageId,
					$domain
				);
			}
		}

		return $jsTranslatorCalls;
	}

	/**
	 * @param \PHPParser_Node $callExprNode
	 * @return boolean
	 */
	private function isTransFunctionCall(JCallExprNode $callExprNode) {
		$functionName = $this->getFunctionName($callExprNode);

		if ($functionName !== null) {
			if (array_key_exists($functionName, $this->transMethodSpecifications)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \JNodeBase $messageIdArgumentNode
	 * @return string
	 */
	private function getMessageId(\JNodeBase $messageIdArgumentNode) {
		try {
			$messageId = $this->getConcatenatedString($messageIdArgumentNode);
		} catch (\SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\UnsupportedNodeException $ex) {
			throw new \SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\ParseException(
				'Cannot parse message ID ' . (string)$messageIdArgumentNode
					. ' at line ' . $messageIdArgumentNode->get_line_num()
					. ', column ' . $messageIdArgumentNode->get_col_num(),
				$ex
			);
		}

		return $messageId;
	}

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return string
	 */
	private function getDomain(JCallExprNode $callExprNode) {
		$functionName = $this->getFunctionName($callExprNode);
		$domainArgumentIndex = $this->transMethodSpecifications[$functionName]->getDomainArgumentIndex();

		$argumentNodes = $this->getArgumentNodes($callExprNode);
		if ($domainArgumentIndex !== null && isset($argumentNodes[$domainArgumentIndex])) {
			try {
				$domain = $this->getConcatenatedString($argumentNodes[$domainArgumentIndex]);
			} catch (\SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\UnsupportedNodeException $ex) {
				throw new \SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\ParseException(
					'Cannot parse domain ' . (string)$argumentNodes[$domainArgumentIndex]
						. ' at line ' . $argumentNodes[$domainArgumentIndex]->get_line_num()
						. ', column ' . $argumentNodes[$domainArgumentIndex]->get_col_num(),
					$ex
				);
			}

			return $domain;
		} else {
			return self::DEFAULT_MESSAGE_DOMAIN;
		}
	}

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return string|null
	 */
	private function getFunctionName(JCallExprNode $callExprNode) {
		$memberExprNodes = $callExprNode->get_nodes_by_symbol(J_MEMBER_EXPR, 1);
		/* @var $memberExprNodes \JMemberExprNode[] */

		if (isset($memberExprNodes[0])) {
			return (string)$memberExprNodes[0];
		}

		return null;
	}

	/**
	 * @param \JCallExprNode[] $callExprNode
	 * @return \JNodeBase[]
	 */
	private function getArgumentNodes(JCallExprNode $callExprNode) {
		$argListNodes = $callExprNode->get_nodes_by_symbol(J_ARG_LIST, 2);
		/* @var $argListNodes \JArgListNode[] */

		$argumentNodes = [];
		if (isset($argListNodes[0])) {
			$argsListNode = $argListNodes[0];
			$argNode = $argsListNode->reset();
			do {
				if ($argNode->scalar_symbol() !== ',') {
					$argumentNodes[] = $argNode;
				}
			} while ($argNode = $argsListNode->next());
		}

		return $argumentNodes;
	}

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return \JNodeBase
	 */
	private function getMessageIdArgumentNode(JCallExprNode $callExprNode) {
		$functionName = $this->getFunctionName($callExprNode);
		$messageIdArgumentIndex = $this->transMethodSpecifications[$functionName]->getMessageIdArgumentIndex();

		$argumentNodes = $this->getArgumentNodes($callExprNode);
		if (!isset($argumentNodes[$messageIdArgumentIndex])) {
			throw new \SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\ParseException(
				'Message ID argument not specified at line ' . $callExprNode->get_line_num()
					. ', column ' . $callExprNode->get_col_num()
			);
		}

		return $argumentNodes[$messageIdArgumentIndex];
	}

	/**
	 * @param \JNodeBase $node
	 * @return string
	 */
	private function getConcatenatedString(JNodeBase $node) {
		if ($node->scalar_symbol() === J_STRING_LITERAL) {
			return $this->parseStringLiteral((string)$node);
		}

		if ($node->scalar_symbol() === J_ADD_EXPR) {
			$concatenatedString = '';

			$addExprNode = $node->reset();
			do {
				if ($addExprNode->scalar_symbol() === J_STRING_LITERAL) {
					$concatenatedString .= $this->parseStringLiteral((string)$addExprNode);
				} elseif ($addExprNode->scalar_symbol() === J_NUMERIC_LITERAL) {
					$concatenatedString .= (string)$addExprNode;
				} elseif ($addExprNode->scalar_symbol() === '+') {
					continue;
				} else {
					throw new \SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\UnsupportedNodeException();
				}
			} while ($addExprNode = $node->next());

			return $concatenatedString;
		}

		throw new \SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\UnsupportedNodeException();
	}

	/**
	 * @param string $stringLiteral
	 * @return string
	 */
	private function parseStringLiteral($stringLiteral) {
		return json_decode($this->normalizeStringLiteral($stringLiteral));
	}

	/**
	 * @param string $stringLiteral
	 * @return string
	 */
	private function normalizeStringLiteral($stringLiteral) {
		$matches = [];
		if (preg_match('/^"(.*)"$/', $stringLiteral, $matches)) {
			$doubleQuotesEscaped = $matches[1];
		} elseif (preg_match("/^'(.*)'$/", $stringLiteral, $matches)) {
			$singleQuotesEscaped = $matches[1];
			$unescaped = preg_replace("/\\\\'/", "'", $singleQuotesEscaped);
			$doubleQuotesEscaped = preg_replace('/"/', '\\"', $unescaped);
		} else {
			throw new \SS6\ShopBundle\Component\Javascript\Parser\Translator\Exception\UnsupportedNodeException();
		}

		return '"' . $doubleQuotesEscaped . '"';
	}

}
