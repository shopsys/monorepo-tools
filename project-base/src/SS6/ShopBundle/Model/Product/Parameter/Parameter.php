<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;

/**
 * @ORM\Table(name="parameter_titles")
 * @ORM\Entity
 */
class Parameter {

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
	private $name;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 */
	public function __construct(ParameterData $parameterData) {
		$this->name = $parameterData->getName();
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
	public function getName() {
		return $this->name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 */
	public function edit(ParameterData $parameterData) {
		$this->name = $parameterData->getName();
	}
	
}
