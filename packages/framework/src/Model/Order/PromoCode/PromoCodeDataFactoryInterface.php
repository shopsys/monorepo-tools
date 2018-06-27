<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode;

interface PromoCodeDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData
     */
    public function create(): PromoCodeData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode $promoCode
     * @return \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData
     */
    public function createFromPromoCode(PromoCode $promoCode): PromoCodeData;
}
