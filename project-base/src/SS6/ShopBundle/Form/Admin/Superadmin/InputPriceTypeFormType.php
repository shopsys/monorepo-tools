<?php

namespace SS6\ShopBundle\Form\Admin\Superadmin;

use SS6\ShopBundle\Form\FormType;
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
		return 'inputPriceType_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$inputPriceTypesLabels = $this->getInputPriceTypesLabels();

		$choices = [];
		foreach (PricingSetting::getInputPriceTypes() as $inputPriceType) {
			$choices[$inputPriceType] = $inputPriceTypesLabels[$inputPriceType];
		}

		$builder
			->add('type', FormType::CHOICE, [
				'choices' => $choices,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte typ vstupní ceny']),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

	/**
	 * @return array
	 */
	private function getInputPriceTypesLabels() {
		return [
			PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT => $this->translator->trans('Bez DPH'),
			PricingSetting::INPUT_PRICE_TYPE_WITH_VAT => $this->translator->trans('S DPH'),
		];
	}

}
