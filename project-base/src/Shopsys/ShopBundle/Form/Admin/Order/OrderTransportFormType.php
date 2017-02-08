<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Order\Item\OrderTransportData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderTransportFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 */
	public function __construct(array $transports) {
		$this->transports = $transports;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'order_transport_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('transport', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->transports, 'name', [], null, 'id'),
				'error_bubbling' => true,
			])
			->add('priceWithVat', FormType::MONEY, [
				'currency' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter unit price with VAT']),
				],
				'error_bubbling' => true,
			])
			->add('vatPercent', FormType::MONEY, [
				'currency' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
				],
				'error_bubbling' => true,
			]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => OrderTransportData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
