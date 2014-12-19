<?php

namespace SS6\ShopBundle\Form\Admin\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\ParameterValueToParameterTextTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductParametersFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private $parameters;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValueToParameterTextTransformer
	 */
	private $parameterValueToParameterTextTransformer;

	public function __construct(
		array $parameters,
		ParameterValueToParameterTextTransformer $parameterValueToParameterTextTransformer
	) {
		$this->parameters = $parameters;
		$this->parameterValueToParameterTextTransformer = $parameterValueToParameterTextTransformer;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'productParameterValue';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('parameter', 'choice', [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->parameters, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyberte parametr']),
				],
			])
			->add(
				$builder
					->create('value', 'localized', [
						'required' => true,
						'main_constraints' => [
							new Constraints\NotBlank(['message' => 'Prosím vyplňte hodnotu parametru']),
						],
					])
					->addModelTransformer($this->parameterValueToParameterTextTransformer)
			);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
