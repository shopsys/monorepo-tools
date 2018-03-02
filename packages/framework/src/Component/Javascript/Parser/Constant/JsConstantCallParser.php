<?php

namespace Shopsys\FrameworkBundle\Component\Javascript\Parser\Constant;

use PLUG\JavaScript\JLexBase; // JLexBase contains J_* constants
use PLUG\JavaScript\JNodes\JNodeBase;
use PLUG\JavaScript\JNodes\nonterminal\JCallExprNode;
use PLUG\JavaScript\JNodes\nonterminal\JProgramNode;
use Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser;
use Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser;

class JsConstantCallParser
{
    const FUNCTION_NAME = 'Shopsys.constant';
    const NAME_ARGUMENT_INDEX = 0;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser
     */
    private $jsFunctionCallParser;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser
     */
    private $jsStringParser;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser $jsFunctionCallParser
     * @param \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser $jsStringParser
     */
    public function __construct(
        JsFunctionCallParser $jsFunctionCallParser,
        JsStringParser $jsStringParser
    ) {
        $this->jsFunctionCallParser = $jsFunctionCallParser;
        $this->jsStringParser = $jsStringParser;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JProgramNode $node
     * @return \Shopsys\FrameworkBundle\Component\Javascript\Parser\Constant\JsConstantCall[]
     */
    public function parse(JProgramNode $node)
    {
        $jsConstantCalls = [];

        $callExprNodes = $node->get_nodes_by_symbol(J_CALL_EXPR);
        /* @var $callExprNodes \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode[] */
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
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return bool
     */
    private function isConstantFunctionCall(JCallExprNode $callExprNode)
    {
        $functionName = $this->jsFunctionCallParser->getFunctionName($callExprNode);

        return $functionName === self::FUNCTION_NAME;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\JNodeBase $constantNameArgumentNode
     * @return string
     */
    private function getConstantName(JNodeBase $constantNameArgumentNode)
    {
        try {
            $constantName = $this->jsStringParser->getConcatenatedString($constantNameArgumentNode);
        } catch (\Shopsys\FrameworkBundle\Component\Javascript\Parser\Exception\UnsupportedNodeException $ex) {
            throw new \Shopsys\FrameworkBundle\Component\Javascript\Parser\Constant\Exception\JsConstantCallParserException(
                'Cannot parse constant name ' . (string)$constantNameArgumentNode
                    . ' at line ' . $constantNameArgumentNode->get_line_num()
                    . ', column ' . $constantNameArgumentNode->get_col_num(),
                $ex
            );
        }

        return $constantName;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return \PLUG\JavaScript\JNodes\JNodeBase
     */
    private function getConstantNameArgumentNode(JCallExprNode $callExprNode)
    {
        $argumentNodes = $this->jsFunctionCallParser->getArgumentNodes($callExprNode);
        if (!isset($argumentNodes[self::NAME_ARGUMENT_INDEX])) {
            throw new \Shopsys\FrameworkBundle\Component\Javascript\Parser\Constant\Exception\JsConstantCallParserException(
                'Constant name argument not specified at line ' . $callExprNode->get_line_num()
                    . ', column ' . $callExprNode->get_col_num()
            );
        }

        return $argumentNodes[self::NAME_ARGUMENT_INDEX];
    }
}
