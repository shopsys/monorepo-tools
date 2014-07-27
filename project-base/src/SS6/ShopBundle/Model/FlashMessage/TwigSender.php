<?php

namespace SS6\ShopBundle\Model\FlashMessage;

use Twig_Environment;

class TwigSender {

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
	public function addError($template, $parameters = array()) {
		$message = $this->twigEnvironment->render($template, $parameters);
		$this->flashMessageBag->addError($message, true);
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addInfo($template, $parameters = array()) {
		$message = $this->twigEnvironment->render($template, $parameters);
		$this->flashMessageBag->addInfo($message, true);
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addSuccess($template, $parameters = array()) {
		$message = $this->twigEnvironment->render($template, $parameters);
		$this->flashMessageBag->addSuccess($message, true);
	}

}
