<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;

class PromoCodeDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $promoCodeFacade = $this->get('shopsys.shop.order.promo_code.promo_code_facade');
        /* @var $promoCodeFacade \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade */
        $promoCodeFacade->create(new PromoCodeData('test', 10.0));
    }
}
