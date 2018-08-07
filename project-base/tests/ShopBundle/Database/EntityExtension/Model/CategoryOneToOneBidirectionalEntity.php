<?php

namespace Tests\ShopBundle\Database\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CategoryOneToOneBidirectionalEntity
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedCategory
     *
     * @ORM\OneToOne(targetEntity="ExtendedCategory", inversedBy="oneToOneBidirectionalEntity")
     * @ORM\JoinColumn(nullable=false, name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedCategory
     */
    public function getCategory(): ExtendedCategory
    {
        return $this->category;
    }

    /**
     * @param \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedCategory $category
     */
    public function setCategory(ExtendedCategory $category): void
    {
        $this->category = $category;
    }
}
