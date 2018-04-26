<?php

namespace Shopsys\FrameworkBundle\Model\Slider;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;

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
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
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

    public function __construct()
    {
        $this->image = new ImageUploadData();
        $this->hidden = false;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItem $sliderItem
     */
    public function setFromEntity(SliderItem $sliderItem)
    {
        $this->name = $sliderItem->getName();
        $this->link = $sliderItem->getLink();
        $this->hidden = $sliderItem->isHidden();
        $this->domainId = $sliderItem->getDomainId();
    }
}
