<?php

namespace Shopsys\ShopBundle\Model\Article;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Shopsys\ShopBundle\Component\Gedmo\SortablePosition;
use Shopsys\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface;

/**
 * @ORM\Table(name="articles")
 * @ORM\Entity
 */
class Article implements OrderableEntityInterface
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
     * @Gedmo\SortableGroup
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $seoTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $seoMetaDescription;

    /**
     * @var string
     *
     * @Gedmo\SortableGroup
     * @ORM\Column(type="text")
     */
    private $placement;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @param \Shopsys\ShopBundle\Model\Article\ArticleData $articleData
     */
    public function __construct(ArticleData $articleData) {
        $this->domainId = $articleData->domainId;
        $this->name = $articleData->name;
        $this->text = $articleData->text;
        $this->seoTitle = $articleData->seoTitle;
        $this->seoMetaDescription = $articleData->seoMetaDescription;
        $this->placement = $articleData->placement;
        $this->position = SortablePosition::LAST_POSITION;
        $this->hidden = $articleData->hidden;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\ArticleData $articleData
     */
    public function edit(ArticleData $articleData) {
        $this->name = $articleData->name;
        $this->text = $articleData->text;
        $this->seoTitle = $articleData->seoTitle;
        $this->seoMetaDescription = $articleData->seoMetaDescription;
        $this->placement = $articleData->placement;
        $this->hidden = $articleData->hidden;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDomainId() {
        return $this->domainId;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @return string|null
     */
    public function getSeoTitle() {
        return $this->seoTitle;
    }

    /**
     * @return string|null
     */
    public function getSeoMetaDescription() {
        return $this->seoMetaDescription;
    }

    /**
     * @return string
     */
    public function getPlacement() {
        return $this->placement;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }

    /**
     * @param string $placement
     */
    public function setPlacement($placement) {
        $this->placement = $placement;
    }

    /**
     * @return bool $visible
     */
    public function isHidden() {
        return $this->hidden;
    }
}
