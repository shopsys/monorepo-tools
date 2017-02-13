<?php

namespace Shopsys\ShopBundle\Component\Javascript\Parser;

use PLUG\JavaScript\JLexBase; // JLexBase contains J_* constants
use PLUG\JavaScript\JNodes\nonterminal\JCallExprNode;

class JsFunctionCallParser
{
    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return string|null
     */
    public function getFunctionName(JCallExprNode $callExprNode)
    {
        $memberExprNodes = $callExprNode->get_nodes_by_symbol(J_MEMBER_EXPR, 1);
        /* @var $memberExprNodes \PLUG\JavaScript\JNodes\nonterminal\JMemberExprNode[] */

        if (isset($memberExprNodes[0])) {
            return (string)$memberExprNodes[0];
        }

        return null;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return \PLUG\JavaScript\JNodes\JNodeBase[]
     */
    public function getArgumentNodes(JCallExprNode $callExprNode)
    {
        $argListNodes = $callExprNode->get_nodes_by_symbol(J_ARG_LIST, 2);
        /* @var $argListNodes \PLUG\JavaScript\JNodes\nonterminal\JArgListNode[] */

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
