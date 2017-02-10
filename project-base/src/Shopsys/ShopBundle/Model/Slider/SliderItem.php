<?php

namespace Shopsys\ShopBundle\Model\Slider;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface;
use Shopsys\ShopBundle\Model\Slider\SliderItemData;

/**
 * SliderItem
 *
 * @ORM\Table(name="slider_items")
 * @ORM\Entity
 */
class SliderItem implements OrderableEntityInterface
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $link;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @param \Shopsys\ShopBundle\Model\Slider\SliderItemData $sliderItemData
     */
    public function __construct(SliderItemData $sliderItemData)
    {
        $this->domainId = $sliderItemData->domainId;
        $this->name = $sliderItemData->name;
        $this->link = $sliderItemData->link;
        $this->hidden = $sliderItemData->hidden;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Slider\SliderItemData $sliderItemData
     */
    public function edit(SliderItemData $sliderItemData)
    {
        $this->name = $sliderItemData->name;
        $this->link = $sliderItemData->link;
        $this->hidden = $sliderItemData->hidden;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }
}
