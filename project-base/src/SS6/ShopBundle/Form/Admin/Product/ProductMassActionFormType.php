<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\MassAction\ProductMassActionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductMassActionFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

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
					ProductMassActionData::SELECT_TYPE_CHECKED => $this->translator->trans('Pouze zaškrtnuté zboží'),
					ProductMassActionData::SELECT_TYPE_ALL_RESULTS => $this->translator->trans('Všechny výsledky hledání'),
				],
			])
			->add('action', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::ACTION_SET => $this->translator->trans('Nastavit'),
				],
			])
			->add('subject', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::SUBJECT_PRODUCT_HIDDEN => $this->translator->trans('Skrývání zboží'),
				],
			])
			->add('value', FormType::CHOICE, [
				'required' => true,
				'choices' => [
					ProductMassActionData::VALUE_PRODUCT_HIDE => $this->translator->trans('Skrýt'),
					ProductMassActionData::VALUE_PRODUCT_SHOW => $this->translator->trans('Zobrazit'),
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
