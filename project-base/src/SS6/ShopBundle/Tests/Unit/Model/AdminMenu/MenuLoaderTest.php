<?php

namespace SS6\ShopBundle\Tests\Unit\Model\AdminNavigation;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\AdminNavigation\MenuLoader;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

/**
 * @UglyTest
 */
class MenuLoaderTest extends FunctionalTestCase {

	public function testLoadFromArray() {
		$testMenu = [
			[
				'label' => 'Item 1',
				'items' => [
					[
						'label' => 'Item 1.1',
						'route' => 'item_1_1',
					],
					[
						'label' => 'Item 1.2',
						'route' => 'item_1_2',
					],
				],
			],
			[
				'label' => 'Item 2',
				'type' => MenuItem::TYPE_SETTINGS,
			],
			[
				'label' => 'Item 3',
				'route' => 'item_3',
			],
		];

		$menuLoader = $this->getMenuLoaderWithMockedTranslator();
		$menu = $menuLoader->loadFromArray($testMenu);

		$this->assertCount(2, $menu->getRegularItems());
		$this->assertCount(2, $menu->getRegularItems()[0]->getItems());
		$this->assertInstanceOf(MenuItem::class, $menu->getSettingsItem());
		$this->assertSame(MenuItem::TYPE_REGULAR, $menu->getRegularItems()[0]->getType());
		$this->assertSame('Item 1 translated', $menu->getRegularItems()[0]->getLabel());
		$this->assertSame('item_3', $menu->getRegularItems()[1]->getRoute());
	}

	public function testLoadFromArrayMissingSettingsItem() {
		$testMenu = [
			[
				'label' => 'Item 1',
				'items' => [
					[
						'label' => 'Item 1.1',
						'route' => 'item_1_1',
					],
					[
						'label' => 'Item 1.2',
						'route' => 'item_1_2',
					],
				],
			],
			[
				'label' => 'Item 3',
				'route' => 'item_3',
			],
		];

		$menuLoader = $this->getMenuLoaderWithMockedTranslator();

		$this->setExpectedException(\SS6\ShopBundle\Model\AdminNavigation\Exception\MissingSettingsItemException::class);
		$menuLoader->loadFromArray($testMenu);
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuLoader
	 */
	private function getMenuLoaderWithMockedTranslator() {
		$translatorMock = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->setMethods(['trans'])
			->getMock();
		$translatorMock->method('trans')
			->willReturnCallback(function ($id) {
				return $id . ' translated';
			});

		return new MenuLoader($this->getContainer()->get('filesystem'), $translatorMock);
	}

}
