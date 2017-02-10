<?php

namespace Shopsys\ShopBundle\Component\Javascript\Parser\Translator;

use PLUG\JavaScript\JNodes\JNodeBase;
use PLUG\JavaScript\JNodes\nonterminal\JCallExprNode;

class JsTranslatorCall
{
    /**
     * @var \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode
     */
    private $callExprNode;

    /**
     * @var \PLUG\JavaScript\JNodes\JNodeBase
     */
    private $messageIdArgumentNode;

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $functionName;

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode $callExprNode
     * @param \PLUG\JavaScript\JNodes\JNodeBase $messageIdArgumentNode
     * @param string $messageId
     * @param string $domain
     */
    public function __construct(
        JCallExprNode $callExprNode,
        JNodeBase $messageIdArgumentNode,
        $messageId,
        $domain,
        $functionName
    ) {
        $this->callExprNode = $callExprNode;
        $this->messageIdArgumentNode = $messageIdArgumentNode;
        $this->messageId = $messageId;
        $this->domain = $domain;
        $this->functionName = $functionName;
    }

    /**
     * @return \PLUG\JavaScript\JNodes\nonterminal\JCallExprNode
     */
    public function getCallExprNode()
    {
        return $this->callExprNode;
    }

    /**
     * @return \PLUG\JavaScript\JNodes\JNodeBase
     */
    public function getMessageIdArgumentNode()
    {
        return $this->messageIdArgumentNode;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }
}
