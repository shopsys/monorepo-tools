<?php

namespace SS6\ShopBundle\Form\Admin\Order\Status;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeleteFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	private $orderStatusesToDelete;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus[] $orderStatusesToDelete
	 */
	public function __construct(array $orderStatusesToDelete) {
		$this->orderStatusesToDelete = $orderStatusesToDelete;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'order_status_delete';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('newStatus', 'choice', array(
				'choice_list' => new ObjectChoiceList($this->orderStatusesToDelete, 'name', array(), null, 'id'),
				'empty_value' => '- vyberte stav -',
			))
			->add('delete', 'submit');
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
