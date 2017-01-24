<?php

namespace SS6\ShopBundle\Model\AdminNavigation;

use SS6\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class MenuLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 * @param \SS6\ShopBundle\Component\Translation\Translator $translator
	 */
	public function __construct(Filesystem $filesystem, Translator $translator) {
		$this->filesystem = $filesystem;
		$this->translator = $translator;
	}

	/**
	 * @param string $filename
	 * @return \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	public function loadFromYaml($filename) {
		$yamlParser = new Parser();

		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$menuConfiguration = new MenuConfiguration();
		$processor = new Processor();

		$inputConfig = $yamlParser->parse(file_get_contents($filename));
		$outputConfig = $processor->processConfiguration($menuConfiguration, [$inputConfig]);

		$menu = $this->loadFromArray($outputConfig);

		return $menu;
	}

	/**
	 * @param array $array
	 * @return \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	public function loadFromArray(array $array) {
		$items = $this->loadItems($array);
		$menu = new Menu($items);

		return $menu;
	}

	/**
	 * @param array $array
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]
	 */
	private function loadItems(array $array) {
		$items = [];

		foreach ($array as $arrayItem) {
			$item = $this->loadItem($arrayItem);
			$items[] = $item;
		}

		return $items;
	}

	/**
	 * @param array $array
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem
	 */
	private function loadItem(array $array) {
		if (isset($array['items'])) {
			$items = $this->loadItems($array['items']);
		} else {
			$items = [];
		}

		$item = new MenuItem(
			$this->translator->trans($array['label']),
			isset($array['type']) ? $array['type'] : null,
			isset($array['route']) ? $array['route'] : null,
			isset($array['route_parameters']) ? $array['route_parameters'] : null,
			isset($array['visible']) ? $array['visible'] : null,
			isset($array['superadmin']) ? $array['superadmin'] : null,
			isset($array['icon']) ? $array['icon'] : null,
			isset($array['multidomain_only']) ? $array['multidomain_only'] : null,
			$items
		);

		return $item;
	}

}
