<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider;

class BreadcrumbController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider
     */
    protected $breadcrumbOverrider;

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider $breadcrumbOverrider
     */
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
