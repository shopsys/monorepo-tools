<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Model\Country\CountryFacade;
use SS6\ShopBundle\Model\Country\CountryInlineEdit;

class CountryController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryInlineEdit
	 */
	private $countryInlineEdit;

	public function __construct(
		CountryFacade $countryFacade,
		CountryInlineEdit $countryInlineEdit
	) {
		$this->countryFacade = $countryFacade;
		$this->countryInlineEdit = $countryInlineEdit;
	}

	/**
	 * @Route("/country/list/")
	 */
	public function listAction() {
		$countryInlineEdit = $this->countryInlineEdit;

		$grid = $countryInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Country/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
