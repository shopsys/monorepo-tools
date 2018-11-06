<?php

namespace Shopsys\FrameworkBundle\Model\AdminNavigation;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const SIDE_MENU_ROOT = 'shopsys.admin_side_menu.configure_root';
    const SIDE_MENU_DASHBOARD = 'shopsys.admin_side_menu.configure_dashboard';
    const SIDE_MENU_ORDERS = 'shopsys.admin_side_menu.configure_orders';
    const SIDE_MENU_CUSTOMERS = 'shopsys.admin_side_menu.configure_customers';
    const SIDE_MENU_PRODUCTS = 'shopsys.admin_side_menu.configure_products';
    const SIDE_MENU_PRICING = 'shopsys.admin_side_menu.configure_pricing';
    const SIDE_MENU_MARKETING = 'shopsys.admin_side_menu.configure_marketing';
    const SIDE_MENU_ADMINISTRATORS = 'shopsys.admin_side_menu.configure_administrators';
    const SIDE_MENU_SETTINGS = 'shopsys.admin_side_menu.configure_settings';

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $menuFactory;

    /**
     * @var \Knp\Menu\ItemInterface
     */
    private $menu;

    /**
     * @param \Knp\Menu\FactoryInterface $menuFactory
     * @param \Knp\Menu\ItemInterface $menu
     */
    public function __construct(FactoryInterface $menuFactory, ItemInterface $menu)
    {
        $this->menuFactory = $menuFactory;
        $this->menu = $menu;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getMenuFactory(): FactoryInterface
    {
        return $this->menuFactory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}
