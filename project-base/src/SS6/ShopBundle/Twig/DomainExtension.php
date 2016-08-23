<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\DomainFacade;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
	 * @var \Symfony\Component\Asset\Packages
	 */
	private $assetPackages;

	public function __construct($domainImagesUrlPrefix, ContainerInterface $container, Packages $assetPackages) {
		$this->domainImagesUrlPrefix = $domainImagesUrlPrefix;
		$this->container = $container;
		$this->assetPackages = $assetPackages;
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
	 * @return \SS6\ShopBundle\Component\Domain\Domain
	 */
	public function getDomain() {
		// Twig extensions are loaded during assetic:dump command,
		// so they cannot be dependent on Domain service
		return $this->container->get(Domain::class);
	}

	/**
	 * @return \SS6\ShopBundle\Component\Domain\DomainFacade
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
	public function getDomainIconHtml($domainId, $size = 'normal') {
		$domainName = $this->getDomain()->getDomainConfigById($domainId)->getName();
		if ($this->getDomainFacade()->existsDomainIcon($domainId)) {
			$src = $this->assetPackages->getUrl(sprintf('%s/%u.png', $this->domainImagesUrlPrefix, $domainId));

			return '
				<span class="in-image in-image--' . $size . '">
					<span
						class="in-image__in"
					>
						<img src="' . htmlspecialchars($src, ENT_QUOTES)
						. '" alt="' . htmlspecialchars($domainId, ENT_QUOTES) . '"'
						. ' title="' . htmlspecialchars($domainName, ENT_QUOTES) . '"/>
					</span>
				</span>';
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
