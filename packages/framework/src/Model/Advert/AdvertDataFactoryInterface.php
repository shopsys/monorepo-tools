<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

interface AdvertDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Advert\AdvertData
     */
    public function create(): AdvertData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\Advert $advert
     * @return \Shopsys\FrameworkBundle\Model\Advert\AdvertData
     */
    public function createFromAdvert(Advert $advert): AdvertData;
}
