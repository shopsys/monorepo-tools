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

	public function getParent() {
		return new SingleCheckboxChoiceType();
	}

	public function getName() {
		return 'transport';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$transportChoices = array();
		foreach ($this->transports as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			$transportChoices[$transport->getId()] = $transport;
		}

		$resolver->setDefaults(array(
			'choice_list' => $this->getChoiceList(),
		));
	}

	private function getChoiceList() {
		$labels = array();
		foreach ($this->transports as $transport) {
			$labels[] = $transport->getName();
		}

		return new ChoiceList($this->transports, $labels);
	}

}
