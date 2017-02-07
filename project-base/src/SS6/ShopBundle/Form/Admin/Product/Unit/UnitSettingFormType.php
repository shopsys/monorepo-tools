<?php

namespace SS6\ShopBundle\Form\Admin\Product\Unit;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class UnitSettingFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\Unit[]
	 */
	private $units;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\Unit[] $units
	 */
	public function __construct(array $units) {
		$this->units = $units;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'unit_setting_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('defaultUnit', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->units, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please choose default unit']),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
