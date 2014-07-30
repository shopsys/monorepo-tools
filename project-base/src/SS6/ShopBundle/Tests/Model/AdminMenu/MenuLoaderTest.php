<?php

namespace SS6\ShopBundle\Tests\Model\AdminNavigation;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\AdminNavigation\MenuLoader;

class MenuLoaderTest extends FunctionalTestCase {

	public function testLoadFromArray() {
		$testMenu = array(
			array(
				'label' => 'Item 1',
				'items' => array(
					array(
						'label' => 'Item 1.1',
						'route' => 'item_1_1',
					),
					array(
						'label' => 'Item 1.2',
						'route' => 'item_1_2',
					),
				),
			),
			array(
				'label' => 'Item 2',
				'type' => MenuItem::TYPE_SETTINGS,
			),
			array(
				'label' => 'Item 3',
				'route' => 'item_3'
			),
		);

		$menuLoader = new MenuLoader($this->getContainer()->get('filesystem'));
		$menu = $menuLoader->loadFromArray($testMenu);

		$this->assertCount(2, $menu->getRegularItems());
		$this->assertCount(2, $menu->getRegularItems()[0]->getItems());
		$this->assertInstanceOf(MenuItem::class, $menu->getSettingsItem());
		$this->assertEquals(MenuItem::TYPE_REGULAR, $menu->getRegularItems()[0]->getType());
		$this->assertEquals('item_3', $menu->getRegularItems()[1]->getRoute());
	}

	public function testLoadFromArrayMissingSettingsItem() {
		$testMenu = array(
			array(
				'label' => 'Item 1',
				'items' => array(
					array(
						'label' => 'Item 1.1',
						'route' => 'item_1_1',
					),
					array(
						'label' => 'Item 1.2',
						'route' => 'item_1_2',
					),
				),
			),
			array(
				'label' => 'Item 3',
				'route' => 'item_3'
			),
		);

		$menuLoader = new MenuLoader($this->getContainer()->get('filesystem'));

		$this->setExpectedException(\SS6\ShopBundle\Model\AdminNavigation\Exception\MissingSettingsItemException::class);
		$menuLoader->loadFromArray($testMenu);
	}

}
