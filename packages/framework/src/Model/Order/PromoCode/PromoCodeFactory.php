<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode;

class PromoCodeFactory implements PromoCodeFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $data
     * @return \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode
     */
    public function create(PromoCodeData $data): PromoCode
    {
        return new PromoCode($data);
    }
}
