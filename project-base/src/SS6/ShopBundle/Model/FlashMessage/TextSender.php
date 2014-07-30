<?php

namespace SS6\ShopBundle\Model\FlashMessage;

class TextSender {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;

	public function __construct(Bag $flashMessageBag) {
		$this->flashMessageBag = $flashMessageBag;
	}

	/**
	 * @param string|array $message
	 */
	public function addError($message) {
		$this->flashMessageBag->addError($message, false);
	}

	/**
	 * @param string|array $message
	 */
	public function addInfo($message) {
		$this->flashMessageBag->addInfo($message, false);
	}

	/**
	 * @param string|array $message
	 */
	public function addSuccess($message) {
		$this->flashMessageBag->addSuccess($message, false);
	}

}
