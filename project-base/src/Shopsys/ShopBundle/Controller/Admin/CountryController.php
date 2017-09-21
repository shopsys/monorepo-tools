<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Model\Country\CountryInlineEdit;

class CountryController extends AdminBaseController
{

    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryInlineEdit
     */
    private $countryInlineEdit;

    public function __construct(
        CountryInlineEdit $countryInlineEdit
    ) {
        $this->countryInlineEdit = $countryInlineEdit;
    }

    /**
     * @Route("/country/list/")
     */
    public function listAction()
    {
        $countryInlineEdit = $this->countryInlineEdit;

        $grid = $countryInlineEdit->getGrid();

        return $this->render('@ShopsysShop/Admin/Content/Country/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }
}
