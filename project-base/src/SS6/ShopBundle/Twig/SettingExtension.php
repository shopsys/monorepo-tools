<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValue;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class SettingExtension extends \Twig_Extension {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $setting;

	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getCssVersion', [$this, 'getCssVersion']),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'setting';
	}

	/**
	 * @return int
	 */
	public function getCssVersion() {
		return $this->setting->get(Setting::CSS_VERSION, SettingValue::DOMAIN_ID_COMMON);
	}

}
