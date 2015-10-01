<?php

namespace SS6\ShopBundle\Tests\Test;

use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Component\Domain\Domain;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	private $client;

	protected function setUpDomain() {
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */
		$domain->switchDomainById(1);
	}

	protected function setUp() {
		parent::setUp();
		$this->setUpDomain();
	}

	/**
	 * @param bool $createNew
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
			$this->client->setServerParameters([
				'PHP_AUTH_USER' => $username,
				'PHP_AUTH_PW' => $password,
			]);
		}

		return $this->client;
	}

	/**
	 * @return \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected function getContainer() {
		return $this->getClient()->getContainer()->get('ss6.auto_services.auto_container');
	}

	/**
	 * @param string $referenceName
	 * @return object
	 */
	protected function getReference($referenceName) {
		$persistentReferenceService = $this->getContainer()->get(PersistentReferenceService::class);
		/* @var $persistentReferenceService \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService */

		return $persistentReferenceService->getReference($referenceName);
	}

}
