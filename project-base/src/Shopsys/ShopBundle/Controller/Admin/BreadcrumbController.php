<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;

class BreadcrumbController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    public function __construct(Breadcrumb $breadcrumb) {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @param string $route
     * @param array|null $parameters
     */
    public function indexAction($route, array $parameters = null) {
        $items = $this->breadcrumb->getItems($route, $parameters);

        return $this->render('@ShopsysShop/Admin/Inline/Breadcrumb/breadcrumb.html.twig', [
            'items' => $items,
        ]);
    }
}
