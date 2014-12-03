<?php

namespace SS6\ShopBundle\Form\Admin\Pricing\Group;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PricingGroupSettingsFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private $pricingGroups;


	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 */
	public function __construct(array $pricingGroups) {
		$this->pricingGroups = $pricingGroups;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'pricing_group_settings';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('defaultPricingGroup', 'choice', array(
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->pricingGroups, 'name', array(), null, 'id'),
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím zadejte výchozí cenovou skupinu')),
				),
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
