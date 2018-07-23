<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider;

class BreadcrumbController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider
     */
    private $breadcrumbOverrider;

    public function __construct(BreadcrumbOverrider $breadcrumbOverrider)
    {
        $this->breadcrumbOverrider = $breadcrumbOverrider;
    }

    public function indexAction()
    {
        return $this->render('@ShopsysFramework/Admin/Inline/Breadcrumb/breadcrumb.html.twig', [
            'breadcrumbOverrider' => $this->breadcrumbOverrider,
        ]);
    }
}
