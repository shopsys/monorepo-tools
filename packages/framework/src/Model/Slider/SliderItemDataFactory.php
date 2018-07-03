<?php

namespace Shopsys\FrameworkBundle\Model\Slider;

class SliderItemDataFactory implements SliderItemDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItemData
     */
    public function create(): SliderItemData
    {
        return new SliderItemData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItem $sliderItem
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItemData
     */
    public function createFromSliderItem(SliderItem $sliderItem): SliderItemData
    {
        $sliderItemData = new SliderItemData();
        $this->fillFromSliderItem($sliderItemData, $sliderItem);

        return $sliderItemData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemData $sliderItemData
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItem $sliderItem
     */
    protected function fillFromSliderItem(SliderItemData $sliderItemData, SliderItem $sliderItem)
    {
        $sliderItemData->name = $sliderItem->getName();
        $sliderItemData->link = $sliderItem->getLink();
        $sliderItemData->hidden = $sliderItem->isHidden();
        $sliderItemData->domainId = $sliderItem->getDomainId();
    }
}
