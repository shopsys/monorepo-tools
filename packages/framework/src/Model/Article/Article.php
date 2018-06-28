<?php

namespace Shopsys\FrameworkBundle\Model\Article;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Shopsys\FrameworkBundle\Component\Grid\Ordering\OrderableEntityInterface;

/**
 * @ORM\Table(name="articles")
 * @ORM\Entity
 */
class Article implements OrderableEntityInterface
{
    const PLACEMENT_TOP_MENU = 'topMenu';
    const PLACEMENT_FOOTER = 'footer';
    const PLACEMENT_NONE = 'none';

    private const GEDMO_SORTABLE_LAST_POSITION = -1;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     *
     * @Gedmo\SortableGroup
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoMetaDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoH1;

    /**
     * @var string
     *
     * @Gedmo\SortableGroup
     * @ORM\Column(type="text")
     */
    protected $placement;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean")
     */
    protected $hidden;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $articleData
     */
    public function __construct(ArticleData $articleData)
    {
        $this->domainId = $articleData->domainId;
        $this->name = $articleData->name;
        $this->text = $articleData->text;
        $this->seoTitle = $articleData->seoTitle;
        $this->seoMetaDescription = $articleData->seoMetaDescription;
        $this->seoH1 = $articleData->seoH1;
        $this->placement = $articleData->placement;
        $this->position = self::GEDMO_SORTABLE_LAST_POSITION;
        $this->hidden = $articleData->hidden;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $articleData
     */
    public function edit(ArticleData $articleData)
    {
        $this->name = $articleData->name;
        $this->text = $articleData->text;
        $this->seoTitle = $articleData->seoTitle;
        $this->seoMetaDescription = $articleData->seoMetaDescription;
        $this->seoH1 = $articleData->seoH1;
        $this->placement = $articleData->placement;
        $this->hidden = $articleData->hidden;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string|null
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @return string|null
     */
    public function getSeoMetaDescription()
    {
        return $this->seoMetaDescription;
    }

    /**
     * @return string|null
     */
    public function getSeoH1()
    {
        return $this->seoH1;
    }

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @param string $placement
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;
    }

    /**
     * @return bool $visible
     */
    public function isHidden()
    {
        return $this->hidden;
    }
}
