<?php

namespace SS6\ShopBundle\Component\Translation\JsTranslatorCallParser;

use JCallExprNode;

class JsTranslatorCall {

	/**
	 * @var \JCallExprNode
	 */
	private $callExprNode;

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
	 * @param string $messageId
	 * @param string $domain
	 */
	public function __construct(JCallExprNode $callExprNode, $messageId, $domain) {
		$this->callExprNode = $callExprNode;
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
