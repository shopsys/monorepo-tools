<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractNativeFixture;
use Shopsys\ShopBundle\Component\Domain\DomainDbFunctionsFacade;

class DomainDbFunctionsDataFixture extends AbstractNativeFixture {

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $domainDbFunctionsFacade = $this->get(DomainDbFunctionsFacade::class);
        /* @var $domainDbFunctionsFacade \Shopsys\ShopBundle\Component\Domain\DomainDbFunctionsFacade */
        $domainDbFunctionsFacade->createDomainDbFunctions();
    }

}
