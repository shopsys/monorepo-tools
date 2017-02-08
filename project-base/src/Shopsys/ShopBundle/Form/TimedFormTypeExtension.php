<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Form\FormTimeProvider;
use SS6\ShopBundle\Component\Form\TimedSpamValidationListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimedFormTypeExtension extends AbstractTypeExtension {

	const MINIMUM_FORM_FILLING_SECONDS = 5;
	const OPTION_ENABLED = 'timed_spam_enabled';
	const OPTION_MINIMUM_SECONDS = 'timed_spam_minimum_seconds';

	/**
	 * @var \SS6\ShopBundle\Component\Form\FormTimeProvider
	 */
	private $formTimeProvider;

	/**
	 * @param \SS6\ShopBundle\Component\Form\FormTimeProvider $formTimeProvider
	 */
	public function __construct(FormTimeProvider $formTimeProvider) {
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
			$this->formTimeProvider,
			$options
		));
	}

	/**
	 * @param \Symfony\Component\Form\FormView $view
	 * @param \Symfony\Component\Form\FormInterface $form
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
		]);
	}

	/**
	 * @return string
	 */
	public function getExtendedType() {
		return 'form';
	}

}
