<?php

namespace Shopsys\FrameworkBundle\Model\Slider;

interface SliderItemDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItemData
     */
    public function create(): SliderItemData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItem $sliderItem
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItemData
     */
    public function createFromSliderItem(SliderItem $sliderItem): SliderItemData;
}
