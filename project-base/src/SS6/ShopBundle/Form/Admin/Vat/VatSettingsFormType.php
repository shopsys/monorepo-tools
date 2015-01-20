<?php

namespace SS6\ShopBundle\Form\Admin\Vat;

use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints;

class VatSettingsFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @var array
	 */
	private $roundingTypes;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 * @param array $roundingTypes
	 * @param \Symfony\Component\Translation\TranslatorInterface $translator
	 */
	public function __construct(
		array $vats,
		array $roundingTypes,
		TranslatorInterface $translator
	) {
		$this->vats = $vats;
		$this->roundingTypes = $roundingTypes;
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'vat_settings';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$roundingTypesLabels = $this->getRoundingTypesLabels();

		$roundingTypesChoices = [];
		foreach ($this->roundingTypes as $roundingType) {
			$roundingTypesChoices[$roundingType] = $roundingTypesLabels[$roundingType];
		}

		$builder
			->add('defaultVat', 'choice', [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím zadejte výchozí výši DPH']),
				],
			])
			->add('roundingType', 'choice', [
				'required' => true,
				'choices' => $roundingTypesChoices,
			])
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

	/**
	 * @return array
	 */
	private function getRoundingTypesLabels() {
		return [
			PricingSetting::ROUNDING_TYPE_HUNDREDTHS => $this->translator->trans('Na setiny (haléře)'),
			PricingSetting::ROUNDING_TYPE_FIFTIES => $this->translator->trans('Na padesátníky'),
			PricingSetting::ROUNDING_TYPE_INTEGER => $this->translator->trans('Na celá čísla (koruny)'),
		];
	}

}
