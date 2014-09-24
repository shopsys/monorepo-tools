<?php

namespace SS6\ShopBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class DomainExtension extends \Twig_Extension {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('getDomain', array($this, 'getDomain')),
			new Twig_SimpleFunction('getDomainName', array($this, 'getDomainNameById')),
		);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function getDomain() {
		// Twig extensions are loaded during assetic:dump command,
		// so they cannot be dependent on Domain service
		return $this->container->get('ss6.shop.domain');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'domain';
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getDomainNameById($domainId) {
		return $this->getDomain()->getDomainConfigById($domainId)->getDomain();
	}

}
