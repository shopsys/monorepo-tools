<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Domain\DomainFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class DomainExtension extends \Twig_Extension {

	/**
	 * @var string
	 */
	private $domainImagesDirRelPath;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @param string $domainImagesDirRelPath
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct($domainImagesDirRelPath, ContainerInterface $container) {
		$this->domainImagesDirRelPath = $domainImagesDirRelPath;
		$this->container = $container;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getDomain', [$this, 'getDomain']),
			new Twig_SimpleFunction('getDomainName', [$this, 'getDomainNameById']),
			new Twig_SimpleFunction('existsDomainIcon', [$this, 'existsDomainIcon']),
			new Twig_SimpleFunction('domainIcon', [$this, 'getDomainIconHtml'], ['is_safe' => ['html']]),
		];
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
	 * @return \SS6\ShopBundle\Model\Domain\DomainFacade
	 */
	private function getDomainFacade() {
		// Twig extensions are loaded during assetic:dump command,
		// so they cannot be dependent on DomainFacade service
		return $this->container->get(DomainFacade::class);
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
		return $this->getDomain()->getDomainConfigById($domainId)->getName();
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getDomainIconHtml($domainId) {
		$domainName = $this->getDomain()->getDomainConfigById($domainId)->getName();
		if ($this->existsDomainIcon($domainId)) {
			$src = sprintf('%s/%u.png', $this->domainImagesDirRelPath, $domainId);

			return '<img src="' . htmlspecialchars($src, ENT_QUOTES)
				. '" alt="' . htmlspecialchars($domainId, ENT_QUOTES) . '"'
				. ' title="' . htmlspecialchars($domainName, ENT_QUOTES) . '"/>';
		} else {
			return '<span
				class="text-in-circle text-in-circle--filled text-in-circle--filled__' . $domainId . '"
				title="' . htmlspecialchars($domainName, ENT_QUOTES) . '">' . $domainId . '</span>';
		}
	}

	/**
	 * @param int $domainId
	 * @return bool
	 */
	public function existsDomainIcon($domainId) {
		return $this->getDomainFacade()->existsDomainIcon($domainId);
	}
}
