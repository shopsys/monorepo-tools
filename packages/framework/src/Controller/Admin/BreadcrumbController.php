<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;

class BreadcrumbController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @param string $route
     * @param array|null $parameters
     */
    public function indexAction($route, array $parameters = null)
    {
        $items = $this->breadcrumb->getItems($route, $parameters);

        return $this->render('@ShopsysFramework/Admin/Inline/Breadcrumb/breadcrumb.html.twig', [
            'items' => $items,
        ]);
    }
}
