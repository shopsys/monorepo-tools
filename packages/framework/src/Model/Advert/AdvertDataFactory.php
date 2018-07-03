<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

class AdvertDataFactory implements AdvertDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Advert\AdvertData
     */
    public function create(): AdvertData
    {
        return new AdvertData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\Advert
     * @return \Shopsys\FrameworkBundle\Model\Advert\AdvertData
     */
    public function createFromAdvert(Advert $advert): AdvertData
    {
        $advertData = new AdvertData();
        $this->fillFromAdvert($advertData, $advert);
        return $advertData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $advertData
     * @param \Shopsys\FrameworkBundle\Model\Advert\Advert $advert
     */
    protected function fillFromAdvert(AdvertData $advertData, Advert $advert)
    {
        $advertData->name = $advert->getName();
        $advertData->type = $advert->getType();
        $advertData->code = $advert->getCode();
        $advertData->link = $advert->getLink();
        $advertData->positionName = $advert->getPositionName();
        $advertData->hidden = $advert->isHidden();
        $advertData->domainId = $advert->getDomainId();
    }
}
