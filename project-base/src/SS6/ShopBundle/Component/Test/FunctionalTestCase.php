<?php

namespace SS6\ShopBundle\Component\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	private $client;

	protected function setUpDomain() {
		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$domain->switchDomainById(1);
	}

	protected function setUp() {
		parent::setUp();
		$this->setUpDomain();
	}

	/**
	 * @return \Symfony\Component\HttpKernel\Client
	 * @param string $username
	 * @param string $password
	 * @return \Symfony\Component\HttpKernel\Client
	 */
	protected function getClient($createNew = false, $username = null, $password = null) {
		if ($createNew) {
			$this->client = $this->createClient();
			$this->setUpDomain();
		} elseif (!isset($this->client)) {
			$this->client = $this->createClient();
		}

		if ($username !== null) {
			$this->client->setServerParameters(array(
				'PHP_AUTH_USER' => $username,
				'PHP_AUTH_PW' => $password,
			));
		}

		return $this->client;
	}

	/**
	 * @return \Symfony\Component\DependencyInjection\Container
	 */
	protected function getContainer() {
		return $this->getClient()->getContainer();
	}

	/**
	 * @param string $referenceName
	 * @return object
	 */
	protected function getReference($referenceName) {
		$persistentReferenceService = $this->getContainer()->get('ss6.shop.data_fixture.persistent_reference_service');
		/* @var $persistentReferenceService \SS6\ShopBundle\Model\DataFixture\PersistentReferenceService */

		return $persistentReferenceService->getReference($referenceName);
	}

}
