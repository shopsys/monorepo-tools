<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode;

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
     * @param string|null $code
     * @param float|null $percent
     */
    public function __construct($code = null, $percent = null)
    {
        $this->code = $code;
        $this->percent = $percent;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode $promoCode
     */
    public function setFromEntity(PromoCode $promoCode)
    {
        $this->code = $promoCode->getCode();
        $this->percent = $promoCode->getPercent();
    }
}
