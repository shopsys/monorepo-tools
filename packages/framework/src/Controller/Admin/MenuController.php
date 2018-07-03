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

    public function menuAction()
    {
        return $this->render('@ShopsysFramework/Admin/Inline/Menu/menu.html.twig', [
            'domainConfigs' => $this->domainFacade->getAllDomainConfigs(),
        ]);
    }

    public function panelAction($route, array $parameters = null)
    {
        $menu = $this->menuFactory->createMenuWithAllowedItems();
        $activePath = $menu->getMenuPath($route, $parameters);

        return $this->render('@ShopsysFramework/Admin/Inline/Menu/panel.html.twig', [
            'visibleItems' => $menu->getVisibleItems(),
            'activePath' => $activePath,
        ]);
    }
}
