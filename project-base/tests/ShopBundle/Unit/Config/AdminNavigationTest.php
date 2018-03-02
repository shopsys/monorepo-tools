<?php

namespace Tests\ShopBundle\Unit\Config;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuFactory;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuLoader;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdminNavigationTest extends FunctionalTestCase
{
    public function testHasSettingsItem()
    {
        $this->assertInstanceOf(MenuItem::class, $this->getMenu()->getSettingsItem());
    }

    public function testResolveRoutes()
    {
        $this->resolveRoutesRecursive($this->getMenu()->getItems());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[] $items
     */
    private function resolveRoutesRecursive(array $items)
    {
        $router = $this->getContainer()->get('router');
        /* @var $router \Symfony\Bundle\FrameworkBundle\Routing\Router */

        foreach ($items as $item) {
            if ($item->isVisible()) {
                if ($item->getRoute() !== null) {
                    $router->generate($item->getRoute(), $item->getRouteParameters());
                }

                $this->resolveRoutesRecursive($item->getItems());
            }
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\Menu
     */
    private function getMenu()
    {
        $authorizationChecker = $this->getMockBuilder(AuthorizationCheckerInterface::class)
            ->setMethods(['isGranted'])
            ->getMockForAbstractClass();
        $authorizationChecker->expects($this->any())
            ->method('isGranted')
            ->willReturn(true);

        $menuFactory = new MenuFactory(
            $this->getContainer()->getParameter('shopsys.admin_navigation.config_filepath'),
            $authorizationChecker,
            $this->getServiceByType(MenuLoader::class),
            $this->getServiceByType(Domain::class)
        );

        return $menuFactory->createMenuWithVisibleItems();
    }
}
