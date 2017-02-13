<?php

namespace Shopsys\ShopBundle\Component\Javascript\Parser\Translator;

use PLUG\JavaScript\JLexBase; // JLexBase contains J_* constants
use PLUG\JavaScript\JNodes\JNodeBase;
use PLUG\JavaScript\JNodes\nonterminal\JCallExprNode;
use PLUG\JavaScript\JNodes\nonterminal\JProgramNode;
use Shopsys\ShopBundle\Component\Javascript\Parser\JsFunctionCallParser;
use Shopsys\ShopBundle\Component\Javascript\Parser\JsStringParser;

class JsTranslatorCallParser
{
    const DEFAULT_MESSAGE_DOMAIN = 'messages';

    /**
     * @var \Shopsys\ShopBundle\Component\Javascript\Parser\JsFunctionCallParser
     */
    private $jsFunctionCallParser;

    /**
     * @var \Shopsys\ShopBundle\Component\Javascript\Parser\JsStringParser
     */
    private $jsStringParser;

    /**
     * @var \Shopsys\ShopBundle\Component\Translation\TransMethodSpecification[]
     */
    private $transMethodSpecifications;

    /**
     * @param \Shopsys\ShopBundle\Component\Javascript\Parser\JsFunctionCallParser $jsFunctionCallParser
     * @param \Shopsys\ShopBundle\Component\Javascript\Parser\JsStringParser $jsStringParser
     * @param \Shopsys\ShopBundle\Component\Translation\TransMethodSpecification[] $transMethodSpecifications
     */
    public function __construct(
        JsFunctionCallParser $jsFunctionCallParser,
        JsStringParser $jsStringParser,
        array $transMethodSpecifications
    ) {
        $this->jsFunctionCallParser = $jsFunctionCallParser;
        $this->jsStringParser = $jsStringParser;

        $this->transMethodSpecifications = [];
        foreach ($transMethodSpecifications as $transMethodSpecification) {
            $methodName = $transMethodSpecification->getMethodName();
            $this->transMethodSpecifications[$methodName] = $transMethodSpecification;
        }
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JProgramNode $node
     * @return \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCall[]
     */
    public function parse(JProgramNode $node)
    {
        $jsTranslatorCalls = [];

        $callExprNodes = $node->get_nodes_by_symbol(J_CALL_EXPR);
        /* @var $callExprNodes \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode[] */
        foreach ($callExprNodes as $callExprNode) {
            if ($this->isTransFunctionCall($callExprNode)) {
                $messageIdArgumentNode = $this->getMessageIdArgumentNode($callExprNode);

                $jsTranslatorCalls[] = new JsTranslatorCall(
                    $callExprNode,
                    $messageIdArgumentNode,
                    $this->getMessageId($messageIdArgumentNode),
                    $this->getDomain($callExprNode),
                    $this->jsFunctionCallParser->getFunctionName($callExprNode)
                );
            }
        }

        return $jsTranslatorCalls;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return bool
     */
    private function isTransFunctionCall(JCallExprNode $callExprNode)
    {
        $functionName = $this->jsFunctionCallParser->getFunctionName($callExprNode);

        if ($functionName !== null) {
            if (array_key_exists($functionName, $this->transMethodSpecifications)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\JNodeBase $messageIdArgumentNode
     * @return string
     */
    private function getMessageId(JNodeBase $messageIdArgumentNode)
    {
        try {
            $messageId = $this->jsStringParser->getConcatenatedString($messageIdArgumentNode);
        } catch (\Shopsys\ShopBundle\Component\Javascript\Parser\Exception\UnsupportedNodeException $ex) {
            throw new \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\Exception\JsTranslatorCallParserException(
                'Cannot parse message ID ' . (string)$messageIdArgumentNode
                    . ' at line ' . $messageIdArgumentNode->get_line_num()
                    . ', column ' . $messageIdArgumentNode->get_col_num(),
                $ex
            );
        }

        return $messageId;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return string
     */
    private function getDomain(JCallExprNode $callExprNode)
    {
        $functionName = $this->jsFunctionCallParser->getFunctionName($callExprNode);
        $domainArgumentIndex = $this->transMethodSpecifications[$functionName]->getDomainArgumentIndex();

        $argumentNodes = $this->jsFunctionCallParser->getArgumentNodes($callExprNode);
        if ($domainArgumentIndex !== null && isset($argumentNodes[$domainArgumentIndex])) {
            try {
                $domain = $this->jsStringParser->getConcatenatedString($argumentNodes[$domainArgumentIndex]);
            } catch (\Shopsys\ShopBundle\Component\Javascript\Parser\Exception\UnsupportedNodeException $ex) {
                throw new \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\Exception\JsTranslatorCallParserException(
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
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @return \PLUG\JavaScript\JNodes\JNodeBase
     */
    private function getMessageIdArgumentNode(JCallExprNode $callExprNode)
    {
        $functionName = $this->jsFunctionCallParser->getFunctionName($callExprNode);
        $messageIdArgumentIndex = $this->transMethodSpecifications[$functionName]->getMessageIdArgumentIndex();

        $argumentNodes = $this->jsFunctionCallParser->getArgumentNodes($callExprNode);
        if (!isset($argumentNodes[$messageIdArgumentIndex])) {
            throw new \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\Exception\JsTranslatorCallParserException(
                'Message ID argument not specified at line ' . $callExprNode->get_line_num()
                    . ', column ' . $callExprNode->get_col_num()
            );
        }

        return $argumentNodes[$messageIdArgumentIndex];
    }
}
