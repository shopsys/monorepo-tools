<?php

namespace Shopsys\ShopBundle\Model\Category\TopCategory;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Category\Category;

/**
 * @ORM\Table(name="categories_top")
 * @ORM\Entity
 */
class TopCategory
{

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Category
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Category\Category")
     * @ORM\JoinColumn(nullable=false, name="category_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\Id
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $domainId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     * @param int $position
     */
    public function __construct(Category $category, $domainId, $position) {
        $this->category = $category;
        $this->domainId = $domainId;
        $this->position = $position;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getCategory() {
        return $this->category;
    }

}
