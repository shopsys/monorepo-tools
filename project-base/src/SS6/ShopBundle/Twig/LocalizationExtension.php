<?php

namespace SS6\ShopBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class LocalizationExtension extends \Twig_Extension {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;

		// Twig extensions are loaded during assetic:dump command,
		// so they cannot be dependent on Domain service (dependency of Localization)
		$this->localization = $container->get('ss6.shop.localization.localization');
	}

	/**
	 * Service "templating.helper.assets" cannot be created in CLI, because service "request" is inactive in CLI
	 *
	 * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper
	 */
	private function getAssetsHelper() {
		return $this->container->get('templating.helper.assets');
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('localeFlag', [$this, 'getLocaleFlagHtml'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * @param string $locale
	 * @return string
	 */
	public function getLocaleFlagHtml($locale, $showTitle = true) {
		$src = $this->getAssetsHelper()->getUrl('assets/admin/images/flags/' . $locale . '.png');

		if ($showTitle) {
			$title = $this->getTitle($locale);
			$html = '<img src="' . htmlspecialchars($src, ENT_QUOTES)
				. '" alt="' . htmlspecialchars($locale, ENT_QUOTES)
				. '" title="' . htmlspecialchars($title, ENT_QUOTES) . '" />';
		} else {
			$html = '<img src="' . htmlspecialchars($src, ENT_QUOTES)
				. '" alt="' . htmlspecialchars($locale, ENT_QUOTES) . '" />';
		}

		return $html;
	}

	/**
	 * @param string $locale
	 * @return string
	 */
	private function getTitle($locale) {
		try {
			$title = $this->localization->getLanguageName($locale);
		} catch (\SS6\ShopBundle\Model\Localization\Exception\InvalidLocaleException $e) {
			$title = '';
		}

		return $title;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'localization';
	}

}
