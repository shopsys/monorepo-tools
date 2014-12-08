<?php

namespace SS6\ShopBundle\Form\Admin\Superadmin;

use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints;

class InputPriceTypeFormType extends AbstractType {

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(TranslatorInterface $translator) {
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'inputPriceType';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$inputPriceTypesLabels = $this->getInputPriceTypesLabels();

		$choices = array();
		foreach (PricingSetting::getInputPriceTypes() as $inputPriceType) {
			$choices[$inputPriceType] = $inputPriceTypesLabels[$inputPriceType];
		}

		$builder
			->add('type', 'choice', array(
				'choices' => $choices,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte typ vstupní ceny')),
				),
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

	/**
	 * @return array
	 */
	private function getInputPriceTypesLabels() {
		return array(
			PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT => $this->translator->trans('Bez DPH'),
			PricingSetting::INPUT_PRICE_TYPE_WITH_VAT => $this->translator->trans('S DPH'),
		);
	}

}
