<?php

namespace Shopsys\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Shopsys\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper;
use Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class DomainHelper extends Module {

	// @codingStandardsIgnoreStart
	/**
	 * {@inheritDoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	public function _before(TestInterface $test) {
	// @codingStandardsIgnoreEnd
		$webDriver = $this->getModule(StrictWebDriver::class);
		/* @var $webDriver \Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver */
		$symfonyHelper = $this->getModule(SymfonyHelper::class);
		/* @var $symfonyHelper \Shopsys\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper */
		$domain = $symfonyHelper->grabServiceFromContainer('shopsys.shop.component.domain');
		/* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

		$domainConfig = $domain->getDomainConfigById(1);

		$webDriver->_reconfigure(['url' => $domainConfig->getUrl()]);
	}

}
