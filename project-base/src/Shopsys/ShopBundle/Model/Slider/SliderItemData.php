<?php

namespace Shopsys\ShopBundle\Model\Slider;

class SliderItemData
{

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $link;

    /**
     * @var string|null
     */
    public $image;

    /**
     * @var bool
     */
    public $hidden;

    /**
     * @var int|null
     */
    public $domainId;

    /**
     * @param string|null $name
     * @param string|null $link
     * @param string|null $image
     * @param bool $hidden
     * @param int|null $domainId
     */
    public function __construct(
        $name = null,
        $link = null,
        $image = null,
        $hidden = false,
        $domainId = null
    ) {
        $this->name = $name;
        $this->link = $link;
        $this->image = $image;
        $this->hidden = $hidden;
        $this->domainId = $domainId;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Slider\SliderItem $sliderItem
     */
    public function setFromEntity(SliderItem $sliderItem) {
        $this->name = $sliderItem->getName();
        $this->link = $sliderItem->getLink();
        $this->hidden = $sliderItem->isHidden();
        $this->domainId = $sliderItem->getDomainId();
    }
}
