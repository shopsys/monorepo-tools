<?php

namespace SS6\ShopBundle\Model\AdminMenu;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class MenuLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->filesystem = $filesystem;
	}

	/**
	 * @param string $filename
	 * @return \SS6\ShopBundle\Model\AdminMenu\Menu Description
	 */
	public function loadFromYaml($filename) {
		$yamlParser = new Parser();

		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$array = $yamlParser->parse(file_get_contents($filename));
		$menu = $this->loadFromArray($array);

		return $menu;
	}

	/**
	 * @param array $array
	 * @return \SS6\ShopBundle\Model\AdminMenu\Menu Description
	 */
	public function loadFromArray(array $array) {
		$items = $this->loadItems($array);
		$menu = new Menu($items);

		return $menu;
	}

	/**
	 * @param array $array
	 * @return \SS6\ShopBundle\Model\AdminMenu\MenuItem[]
	 */
	private function loadItems(array $array) {
		$items = array();

		foreach ($array as $arrayItem) {
			$item = $this->loadItem($arrayItem);
			$items[] = $item;
		}

		return $items;
	}

	/**
	 * @param array $array
	 * @return \SS6\ShopBundle\Model\AdminMenu\MenuItem;
	 */
	private function loadItem(array $array) {
		if (!isset($array['label'])) {
			throw new \SS6\ShopBundle\Model\AdminMenu\Exception\MissingItemLabelException(
				'Item has no label which is mandatory'
			);
		}

		$item = new MenuItem($array['label']);

		if (isset($array['type'])) {
			$item->setType($array['type']);
		}

		if (isset($array['route'])) {
			$item->setRoute($array['route']);
		}

		if (isset($array['route_parameters'])) {
			$item->setRouteParameters($array['route_parameters']);
		}

		if (isset($array['items'])) {
			if (!is_array($array['items'])) {
				throw new \SS6\ShopBundle\Model\AdminMenu\Exception\InvalidItemsFormatException(
					'Items configuration is not an array'
				);
			}

			$subitems = $this->loadItems($array['items']);
			$item->setItems($subitems);
		}

		return $item;
	}

}
