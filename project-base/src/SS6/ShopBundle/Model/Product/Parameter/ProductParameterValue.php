<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="product_parameter_values")
 * @ORM\Entity
 */
class ProductParameterValue {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Parameter\Parameter")
	 */
	private $parameter;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValue
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Parameter\ParameterValue")
	 * @ORM\JoinColumn(name="value_id", referencedColumnName="id", nullable=false)
	 */
	private $value;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData $productParameterValueData
	 */
	public function __construct(ProductParameterValueData $productParameterValueData) {
		$this->product = $productParameterValueData->getProduct();
		$this->parameter = $productParameterValueData->getParameter();
		$this->value = $productParameterValueData->getValue();
	}

	public function getProduct() {
		return $this->product;
	}

	public function getParameter() {
		return $this->parameter;
	}

	public function getValue() {
		return $this->value;
	}

}
