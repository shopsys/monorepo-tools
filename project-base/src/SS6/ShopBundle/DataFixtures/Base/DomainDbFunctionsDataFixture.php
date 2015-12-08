<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractNativeFixture;
use SS6\ShopBundle\Component\Domain\DomainDbFunctionsFacade;

class DomainDbFunctionsDataFixture extends AbstractNativeFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$domainDbFunctionsFacade = $this->get(DomainDbFunctionsFacade::class);
		/* @var $domainDbFunctionsFacade \SS6\ShopBundle\Component\Domain\DomainDbFunctionsFacade */
		$domainDbFunctionsFacade->createDomainDbFunctions();
	}

}
