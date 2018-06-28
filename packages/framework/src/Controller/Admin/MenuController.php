<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Component\Domain\DomainFacade;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuFactory;

class MenuController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuFactory
     */
    private $menuFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainFacade
     */
    private $domainFacade;

    public function __construct(MenuFactory $menuFactory, DomainFacade $domainFacade)
    {
        $this->menuFactory = $menuFactory;
        $this->domainFacade = $domainFacade;
    }

    public function menuAction($route, array $parameters = null)
    {
        $menu = $this->menuFactory->createMenuWithVisibleItems();
        $activePath = $menu->getMenuPath($route, $parameters);

        return $this->render('@ShopsysFramework/Admin/Inline/Menu/menu.html.twig', [
            'menu' => $menu,
            'activePath' => $activePath,
            'domainConfigs' => $this->domainFacade->getAllDomainConfigs(),
        ]);
    }

    public function panelAction($route, array $parameters = null)
    {
        $menu = $this->menuFactory->createMenuWithVisibleItems();
        $activePath = $menu->getMenuPath($route, $parameters);

        if (isset($activePath[1]) && $menu->isRouteMatchingDescendantOfSettings($route, $parameters)) {
            $panelItems = $activePath[1]->getItems();
        } elseif (isset($activePath[0])) {
            $panelItems = $activePath[0]->getItems();
        } else {
            $panelItems = null;
        }

        return $this->render('@ShopsysFramework/Admin/Inline/Menu/panel.html.twig', [
            'items' => $panelItems,
            'activePath' => $activePath,
        ]);
    }
}
