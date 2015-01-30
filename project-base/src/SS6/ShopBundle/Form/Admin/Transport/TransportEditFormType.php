<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Transport\TransportEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TransportEditFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory
	 */
	private $transportFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	private $currencies;

	/**
	 * @param \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory $transportFormTypeFactory
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency[] $currencies
	 */
	public function __construct(TransportFormTypeFactory $transportFormTypeFactory, array $currencies) {
		$this->transportFormTypeFactory = $transportFormTypeFactory;
		$this->currencies = $currencies;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'transport_edit';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('transportData', $this->transportFormTypeFactory->create())
			->add($this->getPricesBuilder($builder))
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @return \Symfony\Component\Form\FormBuilderInterface
	 */
	private function getPricesBuilder(FormBuilderInterface $builder) {
		$pricesBuilder = $builder->create('prices', null, [
			'compound' => true,
		]);
		foreach ($this->currencies as $currency) {
			$pricesBuilder
				->add($currency->getId(), FormType::MONEY, [
					'currency' => false,
					'precision' => 6,
					'required' => true,
					'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Prosím vyplňte cenu']),
						new Constraints\GreaterThanOrEqual([
							'value' => 0,
							'message' => 'Cena musí být větší nebo rovna {{ compared_value }}',
						]),

					],
				]);
		}

		return $pricesBuilder;
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => TransportEditData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}
}
