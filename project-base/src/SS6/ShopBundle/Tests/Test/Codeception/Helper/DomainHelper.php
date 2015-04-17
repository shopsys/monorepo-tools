<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestCase;
use SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper;

class DomainHelper extends Module {

	/**
	 * {@inheritDoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	// @codingStandardsIgnoreStart
	public function _before(TestCase $test) {
	// @codingStandardsIgnoreEnd
		$webDriver = $this->getModule('WebDriver');
		/* @var $webDriver \Codeception\Module\WebDriver */
		$symfonyHelper = $this->getModule(SymfonyHelper::class);
		/* @var $symfonyHelper \SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper */
		$domain = $symfonyHelper->grabServiceFromContainer('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$domainConfig = $domain->getDomainConfigById(1);

		$webDriver->_reconfigure(['url' => $domainConfig->getUrl()]);
	}

}
