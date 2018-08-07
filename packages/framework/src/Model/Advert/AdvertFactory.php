<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

class AdvertFactory implements AdvertFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $data
     * @return \Shopsys\FrameworkBundle\Model\Advert\Advert
     */
    public function create(AdvertData $data): Advert
    {
        return new Advert($data);
    }
}
