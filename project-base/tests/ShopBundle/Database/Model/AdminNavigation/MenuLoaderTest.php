<?php

namespace Tests\ShopBundle\Database\Model\AdminNavigation;

use Shopsys\FrameworkBundle\Component\Translation\Translator;
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
                'route' => 'item_2',
            ],
        ];

        $menuLoader = $this->getMenuLoaderWithMockedTranslator();
        $menu = $menuLoader->loadFromArray($testMenu);

        // There should be 2 items (sub-items should be excluded)
        $this->assertCount(2, $menu->getItems());
        // First item should have 2 sub-items
        $this->assertCount(2, $menu->getItems()[0]->getItems());
        // Item labels should be translated
        $this->assertSame('Item 1 translated', $menu->getItems()[0]->getLabel());
        // Second item should have route of item_2
        $this->assertSame('item_2', $menu->getItems()[1]->getRoute());
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
