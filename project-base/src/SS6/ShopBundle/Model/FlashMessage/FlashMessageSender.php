<?php

namespace SS6\ShopBundle\Model\FlashMessage;

use Twig_Environment;

class FlashMessageSender {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;

	/**
	 * @var \Twig_Environment
	 */
	private $twigEnvironment;

	public function __construct(Bag $flashMessageBag, Twig_Environment $twigEnvironment) {
		$this->flashMessageBag = $flashMessageBag;
		$this->twigEnvironment = $twigEnvironment;
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addErrorTwig($template, $parameters = array()) {
		$message = $this->twigEnvironment->render($template, $parameters);
		$this->flashMessageBag->addError($message, false);
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addInfoTwig($template, $parameters = array()) {
		$message = $this->twigEnvironment->render($template, $parameters);
		$this->flashMessageBag->addInfo($message, false);
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addSuccessTwig($template, $parameters = array()) {
		$message = $this->twigEnvironment->render($template, $parameters);
		$this->flashMessageBag->addSuccess($message, false);
	}

	/**
	 * @param string|array $message
	 */
	public function addError($message) {
		$this->flashMessageBag->addError($message, true);
	}

	/**
	 * @param string|array $message
	 */
	public function addInfo($message) {
		$this->flashMessageBag->addInfo($message, true);
	}

	/**
	 * @param string|array $message
	 */
	public function addSuccess($message) {
		$this->flashMessageBag->addSuccess($message, true);
	}

}
