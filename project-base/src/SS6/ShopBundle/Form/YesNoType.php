<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Transformers\NoopDataTransformer;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Extension\IndexedChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class YesNoType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\NoopDataTransformer
	 */
	private $noopDataTransformer;

	public function __construct(
		Translator $translator,
		NoopDataTransformer $noopDataTransformer
	) {
		$this->translator = $translator;
		$this->noopDataTransformer = $noopDataTransformer;
	}

	/**
	 * {@inheritDoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);

		// workaround for ChoiceType issue: https://github.com/symfony/symfony/issues/15573
		$builder->addViewTransformer(new NoopDataTransformer());
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'choice_list' => new IndexedChoiceList(
				[true, false],
				[
					$this->translator->trans('Ano'),
					$this->translator->trans('Ne'),
				],
				['yes', 'no'],
				['1', '0']
			),
			'multiple' => false,
			'expanded' => true,
			'placeholder' => false,
		]);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'choice';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'yes_no';
	}

}
