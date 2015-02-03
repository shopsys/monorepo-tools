<?php

namespace SS6\ShopBundle\Component\Javascript\Parser;

import('PLUG.JavaScript.JLexBase'); // contains J_* constants
import('PLUG.JavaScript.JNodes.nonterminal.JCallExprNode');

use JCallExprNode;

class JsFunctionCallParser {

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return string|null
	 */
	public function getFunctionName(JCallExprNode $callExprNode) {
		$memberExprNodes = $callExprNode->get_nodes_by_symbol(J_MEMBER_EXPR, 1);
		/* @var $memberExprNodes \JMemberExprNode[] */

		if (isset($memberExprNodes[0])) {
			return (string)$memberExprNodes[0];
		}

		return null;
	}

	/**
	 * @param \JCallExprNode $callExprNode
	 * @return \JNodeBase[]
	 */
	public function getArgumentNodes(JCallExprNode $callExprNode) {
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

}
