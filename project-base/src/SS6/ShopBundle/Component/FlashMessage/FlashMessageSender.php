<?php

namespace SS6\ShopBundle\Component\FlashMessage;

use JMS\TranslationBundle\Annotation\Ignore;
use SS6\ShopBundle\Component\Translation\Translator;
use Twig_Environment;

class FlashMessageSender {

	/**
	 * @var \SS6\ShopBundle\Component\FlashMessage\Bag
	 */
	private $flashMessageBag;

	/**
	 * @var \Twig_Environment
	 */
	private $twigEnvironment;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		Bag $flashMessageBag,
		Twig_Environment $twigEnvironment,
		Translator $translator
	) {
		$this->flashMessageBag = $flashMessageBag;
		$this->twigEnvironment = $twigEnvironment;
		$this->translator = $translator;
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addErrorFlashTwig($template, $parameters = []) {
		/** @Ignore */
		$translatedTemplate = $this->translator->trans($template);
		$message = $this->twigEnvironment->render($translatedTemplate, $parameters);
		$this->flashMessageBag->addError($message, false);
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addInfoFlashTwig($template, $parameters = []) {
		/** @Ignore */
		$translatedTemplate = $this->translator->trans($template);
		$message = $this->twigEnvironment->render($translatedTemplate, $parameters);
		$this->flashMessageBag->addInfo($message, false);
	}

	/**
	 * @param string $template
	 * @param array $parameters
	 */
	public function addSuccessFlashTwig($template, $parameters = []) {
		/** @Ignore */
		$translatedTemplate = $this->translator->trans($template);
		$message = $this->twigEnvironment->render($translatedTemplate, $parameters);
		$this->flashMessageBag->addSuccess($message, false);
	}

	/**
	 * @param string|array $message
	 */
	public function addErrorFlash($message) {
		/** @Ignore */
		$translatedMessage = $this->translator->trans($message);
		$this->flashMessageBag->addError($translatedMessage, true);
	}

	/**
	 * @param string|array $message
	 */
	public function addInfoFlash($message) {
		/** @Ignore */
		$translatedMessage = $this->translator->trans($message);
		$this->flashMessageBag->addInfo($translatedMessage, true);
	}

	/**
	 * @param string|array $message
	 */
	public function addSuccessFlash($message) {
		/** @Ignore */
		$translatedMessage = $this->translator->trans($message);
		$this->flashMessageBag->addSuccess($translatedMessage, true);
	}

}
