<?php

namespace SS6\ShopBundle\Form\Admin\Product\Parameter;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductParameterValueFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private $parameters;

	public function __construct(array $parameters) {
		$this->parameters = $parameters;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product_parameter_value_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('parameter', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->parameters, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyberte parametr']),
				],
			])
			->add('valueText', FormType::LOCALIZED, [
				'required' => true,
				'main_constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte hodnotu parametru']),
				],
				'options' => [
					'constraints' => [
						new Constraints\Length(['max' => 255, 'maxMessage' => 'Název nesmí být delší než {{ limit }} znaků']),
					],
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'data_class' => ProductParameterValuesLocalizedData::class,
		]);
	}

}
