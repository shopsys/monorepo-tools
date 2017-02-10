<?php

namespace Shopsys\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Product\Parameter\Parameter;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue;
use Shopsys\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_parameter_values")
 * @ORM\Entity
 */
class ProductParameterValue
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Product")
     * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Parameter\Parameter")
     * @ORM\JoinColumn(nullable=false, name="parameter_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parameter;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue")
     * @ORM\JoinColumn(name="value_id", referencedColumnName="id", nullable=false)
     */
    private $value;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\Parameter $parameter
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue $value
     */
    public function __construct(
        Product $product,
        Parameter $parameter,
        ParameterValue $value
    ) {
        $this->product = $product;
        $this->parameter = $parameter;
        $this->value = $value;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function getParameter() {
        return $this->parameter;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue
     */
    public function getValue() {
        return $this->value;
    }

}
