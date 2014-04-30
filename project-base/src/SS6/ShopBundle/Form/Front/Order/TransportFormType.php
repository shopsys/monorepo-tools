<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Form\SingleCheckboxChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
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
	 * @return \SS6\ShopBundle\Form\SingleCheckboxChoiceType
	 */
	public function getParent() {
		return new SingleCheckboxChoiceType();
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
		$resolver->setDefaults(array(
			'choice_list' => $this->getChoiceList(),
		));
	}

	/**
	 * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList
	 */
	private function getChoiceList() {
		$labels = array();
		foreach ($this->transports as $transport) {
			$labels[] = $transport->getName();
		}

		return new ChoiceList($this->transports, $labels);
	}

}
