<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Extension\IndexedChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class YesNoType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
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
