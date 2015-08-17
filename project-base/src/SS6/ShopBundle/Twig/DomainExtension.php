<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\DomainFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;
use Twig_SimpleFunction;

class DomainExtension extends \Twig_Extension {

	/**
	 * @var string
	 */
	private $domainImagesUrlPrefix;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @param string $domainImagesUrlPrefix
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct($domainImagesUrlPrefix, ContainerInterface $container) {
		$this->domainImagesUrlPrefix = $domainImagesUrlPrefix;
		$this->container = $container;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getDomain', [$this, 'getDomain']),
			new Twig_SimpleFunction('getDomainName', [$this, 'getDomainNameById']),
			new Twig_SimpleFunction('domainIcon', [$this, 'getDomainIconHtml'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function getDomain() {
		// Twig extensions are loaded during assetic:dump command,
		// so they cannot be dependent on Domain service
		return $this->container->get(Domain::class);
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
	 * Service "templating.helper.assets" cannot be created in CLI, because service "request" is inactive in CLI
	 *
	 * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper
	 */
	private function getAssetsHelper() {
		return $this->container->get(CoreAssetsHelper::class);
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
	public function getDomainIconHtml($domainId, $size = 'normal') {
		$domainName = $this->getDomain()->getDomainConfigById($domainId)->getName();
		if ($this->getDomainFacade()->existsDomainIcon($domainId)) {
			$src = $this->getAssetsHelper()->getUrl(sprintf('%s/%u.png', $this->domainImagesUrlPrefix, $domainId));

			return '<img src="' . htmlspecialchars($src, ENT_QUOTES)
				. '" alt="' . htmlspecialchars($domainId, ENT_QUOTES) . '"'
				. ' title="' . htmlspecialchars($domainName, ENT_QUOTES) . '"/>';
		} else {
			return '
				<span class="in-image in-image--' . $size . '">
					<span
						class="in-image__in in-image__in--' . $domainId . '"
						title="' . htmlspecialchars($domainName, ENT_QUOTES) . '"
					>' . $domainId . '</span>
				</span>
			';
		}
	}
}
