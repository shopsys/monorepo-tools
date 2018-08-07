<?php

namespace Shopsys\FrameworkBundle\Model\Slider;

class SliderItemFactory implements SliderItemFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemData $data
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItem
     */
    public function create(SliderItemData $data): SliderItem
    {
        return new SliderItem($data);
    }
}
