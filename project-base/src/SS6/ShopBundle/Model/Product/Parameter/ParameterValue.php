<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValueData;

/**
 * @ORM\Table(name="parameter_values")
 * @ORM\Entity
 */
class ParameterValue {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $text;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterValueData $parameterData
	 */
	public function __construct(ParameterValueData $parameterData) {
		$this->text = $parameterData->getText();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterValueData $parameterData
	 */
	public function edit(ParameterValueData $parameterData) {
		$this->text = $parameterData->getText();
	}

}
