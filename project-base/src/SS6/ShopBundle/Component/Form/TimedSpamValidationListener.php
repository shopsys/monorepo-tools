<?php

namespace SS6\ShopBundle\Component\Form;

use SS6\ShopBundle\Component\Form\FormTimeProvider;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\TimedFormTypeExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TimedSpamValidationListener implements EventSubscriberInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Component\Form\FormTimeProvider
	 */
	private $formTimeProvider;

	/**
	 * @var string[]
	 */
	private $options;

	/**
	 * @param \SS6\ShopBundle\Component\Translation\Translator $translator
	 * @param \SS6\ShopBundle\Component\Form\FormTimeProvider $formTimeProvider
	 * @param array $options
	 */
	public function __construct(Translator $translator, FormTimeProvider $formTimeProvider, array $options) {
		$this->translator = $translator;
		$this->formTimeProvider = $formTimeProvider;
		$this->options = $options;
	}

	/**
	 * @param \Symfony\Component\Form\FormEvent $event
	 */
	public function preSubmit(FormEvent $event) {
		$form = $event->getForm();
		if ($form->isRoot() &&
			$form->getConfig()->getOption('compound') &&
			!$this->formTimeProvider->isFormTimeValid($form->getName(), $this->options)
		) {
			$message = $this->translator->transChoice(
				'{1} Před odesláním formuláře musíte počkat %seconds% vteřinu.
				|[2,4] Před odesláním formuláře musíte počkat %seconds% vteřiny.
				|[5,Inf] Před odesláním formuláře musíte počkat %seconds% vteřin.',
				$this->options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS],
				[
					'%seconds%' => $this->options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS],
				]
			);
			$form->addError(new FormError($message));
		}
		$this->formTimeProvider->removeFormTime($form->getName());
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents() {
		return [
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}

}
