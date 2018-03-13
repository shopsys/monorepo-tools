<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractNativeFixture;
use Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade;

class DomainDbFunctionsDataFixture extends AbstractNativeFixture
{
    /** @var \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade */
    private $domainDbFunctionsFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade $domainDbFunctionsFacade
     */
    public function __construct(DomainDbFunctionsFacade $domainDbFunctionsFacade)
    {
        $this->domainDbFunctionsFacade = $domainDbFunctionsFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->domainDbFunctionsFacade->createDomainDbFunctions();
    }
}
