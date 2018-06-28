<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Model\Country\CountryInlineEdit;

class CountryController extends AdminBaseController
{

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryInlineEdit
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

        return $this->render('@ShopsysFramework/Admin/Content/Country/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }
}
