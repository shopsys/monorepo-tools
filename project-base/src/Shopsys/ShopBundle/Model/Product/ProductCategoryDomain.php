<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_category_domains")
 * @ORM\Entity
 */
class ProductCategoryDomain
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Product", inversedBy="productCategoryDomains")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $product;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Category
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Category\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     */
    public function __construct(Product $product, Category $category, $domainId) {
        $this->product = $product;
        $this->category = $category;
        $this->domainId = $domainId;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @return int
     */
    public function getDomainId() {
        return $this->domainId;
    }
}
