<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode;

class PromoCodeData
{
    /**
     * @var string|null
     */
    public $code;

    /**
     * @var float|null
     */
    public $percent;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCode $promoCode
     */
    public function setFromEntity(PromoCode $promoCode)
    {
        $this->code = $promoCode->getCode();
        $this->percent = $promoCode->getPercent();
    }
}
