<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Translator;

import('PLUG.JavaScript.JNodes.JNodeBase');
import('PLUG.JavaScript.JNodes.nonterminal.JCallExprNode');

use JCallExprNode;
use JNodeBase;

class JsTranslatorCall {

	/**
	 * @var \JCallExprNode
	 */
	private $callExprNode;

	/**
	 * @var \JNodeBase
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
	 * @param \JCallExprNode $callExprNode
	 * @param \JNodeBase $messageIdArgumentNode
	 * @param string $messageId
	 * @param string $domain
	 */
	public function __construct(
		JCallExprNode $callExprNode,
		JNodeBase $messageIdArgumentNode,
		$messageId,
		$domain
	) {
		$this->callExprNode = $callExprNode;
		$this->messageIdArgumentNode = $messageIdArgumentNode;
		$this->messageId = $messageId;
		$this->domain = $domain;
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
	public function getMessageIdArgumentNode() {
		return $this->messageIdArgumentNode;
	}

	/**
	 * @return string
	 */
	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

}
