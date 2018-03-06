<?php

namespace Tests\ShopBundle\Unit\Model\AdminNavigation;

use Shopsys\FrameworkBundle\Component\Translation\Translator;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuLoader;
use Tests\ShopBundle\Test\FunctionalTestCase;

class MenuLoaderTest extends FunctionalTestCase
{
    public function testLoadFromArray()
    {
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

        // There should be 2 regular items (sub-items and settings items should be excluded)
        $this->assertCount(2, $menu->getRegularItems());
        // First regular item should have 2 sub-items
        $this->assertCount(2, $menu->getRegularItems()[0]->getItems());
        // Settings item should be only one instance of MenuItem class
        $this->assertInstanceOf(MenuItem::class, $menu->getSettingsItem());
        // First regular item should be of type regular
        $this->assertSame(MenuItem::TYPE_REGULAR, $menu->getRegularItems()[0]->getType());
        // Item labels should be translated
        $this->assertSame('Item 1 translated', $menu->getRegularItems()[0]->getLabel());
        // Second regular item should have route of item_3 as the settings item should be skipped
        $this->assertSame('item_3', $menu->getRegularItems()[1]->getRoute());
    }

    public function testLoadFromArrayMissingSettingsItem()
    {
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

        $this->expectException(\Shopsys\FrameworkBundle\Model\AdminNavigation\Exception\MissingSettingsItemException::class);
        $menuLoader->loadFromArray($testMenu);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuLoader
     */
    private function getMenuLoaderWithMockedTranslator()
    {
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
