<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode;

interface PromoCodeFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $data
     * @return \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode
     */
    public function create(PromoCodeData $data): PromoCode;
}
