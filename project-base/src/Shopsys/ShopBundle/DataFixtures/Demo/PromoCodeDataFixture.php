<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;

class PromoCodeDataFixture extends AbstractReferenceFixture {

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $promoCodeFacade = $this->get(PromoCodeFacade::class);
        /* @var $promoCodeFacade \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade */
        $promoCodeFacade->create(new PromoCodeData('test', 10.0));
    }
}
