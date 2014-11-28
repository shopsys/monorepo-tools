<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Model\Product\ProductListOrderingSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderingSettingFormType extends AbstractType {

	/**
	 * @param string $orderingMode
	 * @return string
	 */
	private function getOrderingModeName($orderingMode) {
		$orderingModeNames = array(
			ProductListOrderingSetting::ORDER_BY_NAME_ASC => 'abecedně A -> Z',
			ProductListOrderingSetting::ORDER_BY_NAME_DESC => 'abecedně Z -> A',
			ProductListOrderingSetting::ORDER_BY_PRICE_ASC => 'od nejlevnějšího',
			ProductListOrderingSetting::ORDER_BY_PRICE_DESC => 'od nejdražšího',
		);

		return $orderingModeNames[$orderingMode];
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$orderingModes = ProductListOrderingSetting::getOrderingModes();

		$orderingChoices = array();
		foreach ($orderingModes as $orderingMode) {
			$orderingChoices[$orderingMode] = $this->getOrderingModeName($orderingMode);
		}

		$builder
			->add('orderingMode', 'choice', array(
				'choices' => $orderingChoices,
			));
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'productListOrderingSetting';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
