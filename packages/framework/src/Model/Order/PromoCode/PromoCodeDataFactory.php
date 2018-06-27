<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode;

class PromoCodeDataFactory implements PromoCodeDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData
     */
    public function create(): PromoCodeData
    {
        return new PromoCodeData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode $promoCode
     * @return \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData
     */
    public function createFromPromoCode(PromoCode $promoCode): PromoCodeData
    {
        $promoCodeData = new PromoCodeData();
        $this->fillFromPromoCode($promoCodeData, $promoCode);

        return $promoCodeData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode $promoCode
     */
    protected function fillFromPromoCode(PromoCodeData $promoCodeData, PromoCode $promoCode)
    {
        $promoCodeData->code = $promoCode->getCode();
        $promoCodeData->percent = $promoCode->getPercent();
    }
}
