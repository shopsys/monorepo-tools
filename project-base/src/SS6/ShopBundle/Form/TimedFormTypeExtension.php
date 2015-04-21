<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Form\FormTimeProvider;
use SS6\ShopBundle\Component\Form\TimedSpamValidationListener;
use SS6\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimedFormTypeExtension extends AbstractTypeExtension {

	const MINIMUM_FORM_FILLING_SECONDS = 5;
	const OPTION_ENABLED = 'timed_spam_enabled';
	const OPTION_MINIMUM_SECONDS = 'timed_spam_minimum_seconds';
	const OPTION_MESSAGE = 'timed_spam_message';

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Component\Form\FormTimeProvider
	 */
	private $formTimeProvider;

	/**
	 * @param \SS6\ShopBundle\Component\Translation\Translator $translator
	 * @param \SS6\ShopBundle\Component\Form\FormTimeProvider $formTimeProvider
	 */
	public function __construct(Translator $translator, FormTimeProvider $formTimeProvider) {
		$this->translator = $translator;
		$this->formTimeProvider = $formTimeProvider;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		if (!$options[self::OPTION_ENABLED]) {
			return;
		}

		$builder->addEventSubscriber(new TimedSpamValidationListener(
			$this->translator, $this->formTimeProvider, $options
		));
	}

	/**
	 * @param \SS6\ShopBundle\Form\FormView $view
	 * @param \SS6\ShopBundle\Form\FormInterface $form
	 * @param array $options
	 */
	public function finishView(FormView $view, FormInterface $form, array $options) {
		if ($options[self::OPTION_ENABLED] && !$view->parent && $options['compound']) {
			$this->formTimeProvider->generateFormTime($form->getName());
		}
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			self::OPTION_ENABLED => false,
			self::OPTION_MINIMUM_SECONDS => self::MINIMUM_FORM_FILLING_SECONDS,
			self::OPTION_MESSAGE => '{1} Před odesláním formuláře musíte počkat %seconds% vteřinu.
				|[2,4] Před odesláním formuláře musíte počkat %seconds% vteřiny.
				|[5,Inf] Před odesláním formuláře musíte počkat %seconds% vteřin.',
		]);
	}

	/**
	 * @return string
	 */
	public function getExtendedType() {
		return 'form';
	}

}
