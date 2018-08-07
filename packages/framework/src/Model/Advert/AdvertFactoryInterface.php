<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

interface AdvertFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $data
     * @return \Shopsys\FrameworkBundle\Model\Advert\Advert
     */
    public function create(AdvertData $data): Advert;
}
