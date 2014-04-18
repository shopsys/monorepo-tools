<?php

namespace SS6\ShopBundle\Component\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase {
	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	private $client;
	
	/**
	 * @return \Symfony\Component\DependencyInjection\Container
	 */
	protected function getContainer() {
		if (!isset($this->client)) {
			$this->client = $this->createClient();
		}
		
		return $this->client->getContainer();
	}
}
