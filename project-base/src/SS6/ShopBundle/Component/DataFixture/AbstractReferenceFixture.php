<?php

namespace SS6\ShopBundle\Component\DataFixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use SS6\Environment;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractReferenceFixture extends AbstractFixture implements ContainerAwareInterface {

	/**
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	protected $container;

	/**
	 * @var \Symfony\Component\HttpKernel\Kernel
	 */
	private $kernel;

	/**
	 * @var \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService
	 */
	private $persistentReferenceService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
		$this->kernel = $this->get('kernel');
		$this->persistentReferenceService = $this->get('ss6.shop.data_fixture.persistent_reference_service');
	}

	/**
	 * @param string $serviceId
	 * @return mixed
	 */
	protected function get($serviceId) {
		return $this->container->get($serviceId);
	}

	/**
	 * @param string $name
	 * @param object $object
	 * @param bool $persistent
	 */
	public function addReference($name, $object, $persistent = true) {
		parent::addReference($name, $object);

		if ($persistent && $this->kernel->getEnvironment() === Environment::ENVIRONMENT_TEST) {
			$this->persistentReferenceService->persistReference($name, $object);
		}
	}

	/**
	 * @param string $name
	 * @param object $object
	 * @param bool $persistent
	 */
	public function setReference($name, $object, $persistent = true) {
		parent::setReference($name, $object);

		if ($persistent && $this->kernel->getEnvironment() === Environment::ENVIRONMENT_TEST) {
			$this->persistentReferenceService->persistReference($name, $object);
		}
	}

}
