<?php

namespace SS6\ShopBundle\Component\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	private $client;

	protected function setUp() {
		parent::setUp();

		$domain = $this->getContainer()->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$domain->switchDomainById(1);
	}

	/**
	 * @return \Symfony\Component\HttpKernel\Client
	 */
	protected function getClient() {
		if (!isset($this->client)) {
			$this->client = $this->createClient();
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

	/**
	 * @param string $username
	 * @param string $password
	 */
	protected function authenticateUser($username, $password) {
		$this->getClient()->setServerParameters(array(
			'PHP_AUTH_USER' => $username,
			'PHP_AUTH_PW' => $password,
		));
	}

}
