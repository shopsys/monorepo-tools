<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractNativeFixture;

class DomainDbFunctionsDataFixture extends AbstractNativeFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $domainDbFunctionsFacade = $this->get('shopsys.shop.component.domain.domain_db_functions_facade');
        /* @var $domainDbFunctionsFacade \Shopsys\ShopBundle\Component\Domain\DomainDbFunctionsFacade */
        $domainDbFunctionsFacade->createDomainDbFunctions();
    }
}
