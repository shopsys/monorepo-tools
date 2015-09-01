<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TransportFormType extends AbstractType {
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
	 * {@inheritDoc}
	 */
	public function getParent() {
		return FormType::SINGLE_CHECKBOX_CHOICE;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'transport';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'choice_list' => new ObjectChoiceList($this->transports, 'name', [], null, 'id'),
		]);
	}

}
