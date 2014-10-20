<?php

namespace SS6\ShopBundle\Tests\Model\Domain;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\HttpFoundation\Request;

class DomainTest extends PHPUnit_Framework_TestCase {

	public function testGetIdNotSet() {
		$domainConfigs = array(
			new DomainConfig(1, 'example.com', 'design1'),
			new DomainConfig(2, 'example.org', 'design2'),
		);

		$domain = new Domain($domainConfigs);
		$this->setExpectedException(\SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException::class);
		$domain->getId();
	}

	public function testSwitchDomainByRequest() {
		$domainConfigs = array(
			new DomainConfig(1, 'example.com', 'design1'),
			new DomainConfig(2, 'example.org', 'design2'),
		);

		$domain = new Domain($domainConfigs);

		$requestMock = $this->getMockBuilder(Request::class)
			->setMethods(array('getHost'))
			->getMock();
		$requestMock->expects($this->atLeastOnce())->method('getHost')->will($this->returnValue('example.com'));

		$domain->switchDomainByRequest($requestMock);
		$this->assertEquals(1, $domain->getId());
		$this->assertEquals('design1', $domain->getTemplatesDirectory());
	}

	public function testGetAll() {
		$domainConfigs = array(
			new DomainConfig(1, 'example.com', 'design1'),
			new DomainConfig(2, 'example.org', 'design2'),
		);

		$domain = new Domain($domainConfigs);

		$this->assertEquals($domainConfigs, $domain->getAll());
	}

	public function testGetDomainConfigById() {
		$domainConfigs = array(
			new DomainConfig(1, 'example.com', 'design1'),
			new DomainConfig(2, 'example.org', 'design2'),
		);

		$domain = new Domain($domainConfigs);

		$this->assertEquals($domainConfigs[0], $domain->getDomainConfigById(1));
		$this->assertEquals($domainConfigs[1], $domain->getDomainConfigById(2));

		$this->setExpectedException(\SS6\ShopBundle\Model\Domain\Exception\InvalidDomainIdException::class);
		$domain->getDomainConfigById(3);
	}

}
