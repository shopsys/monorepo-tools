<?php

namespace SS6\ShopBundle\Form\Admin\Vat;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 * @param array $roundingTypes
	 */
	public function __construct(array $vats, array $roundingTypes) {
		$this->vats = $vats;
		$this->roundingTypes = $roundingTypes;
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
		$builder
			->add('defaultVat', 'choice', array(
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', array(), null, 'id'),
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím zadejte výchozí výši DPH')),
				),
			))
			->add('roundingType', 'choice', array(
				'required' => true,
				'choices' => $this->roundingTypes,
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
