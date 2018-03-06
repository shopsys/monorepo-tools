<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractNativeFixture;
use Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade;

class DomainDbFunctionsDataFixture extends AbstractNativeFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $domainDbFunctionsFacade = $this->get(DomainDbFunctionsFacade::class);
        /* @var $domainDbFunctionsFacade \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade */
        $domainDbFunctionsFacade->createDomainDbFunctions();
    }
}
