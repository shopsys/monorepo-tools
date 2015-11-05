<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\MassAction\ProductMassActionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductMassActionFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'mass_action_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('selectType', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::SELECT_TYPE_CHECKED => t('Pouze zaškrtnuté zboží'),
					ProductMassActionData::SELECT_TYPE_ALL_RESULTS => t('Všechny výsledky hledání'),
				],
			])
			->add('action', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::ACTION_SET => t('Nastavit'),
				],
			])
			->add('subject', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::SUBJECT_PRODUCT_HIDDEN => t('Skrývání zboží'),
				],
			])
			->add('value', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::VALUE_PRODUCT_HIDE => t('Skrýt'),
					ProductMassActionData::VALUE_PRODUCT_SHOW => t('Zobrazit'),
				],
			])
			->add('submit', FormType::SUBMIT);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'data_class' => ProductMassActionData::class,
		]);
	}

}
