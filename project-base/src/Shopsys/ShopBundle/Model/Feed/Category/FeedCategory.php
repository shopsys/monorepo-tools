<?php

namespace Shopsys\ShopBundle\Model\Feed\Category;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryData;

/**
 * @ORM\Table(name="feed_categories")
 * @ORM\Entity
 */
class FeedCategory
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     */
    private $extId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $fullName;

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryData $feedCategoryData
     */
    public function __construct(FeedCategoryData $feedCategoryData) {
        $this->extId = $feedCategoryData->extId;
        $this->name = $feedCategoryData->name;
        $this->fullName = $feedCategoryData->fullName;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryData $feedCategoryData
     */
    public function edit(FeedCategoryData $feedCategoryData) {
        $this->name = $feedCategoryData->name;
        $this->fullName = $feedCategoryData->fullName;
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
     * @return string
     */
    public function getFullName() {
        return $this->fullName;
    }
}
