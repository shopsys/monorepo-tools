<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OverviewController extends Controller {

	/**
	 * @Route("/overview/")
	 */
	public function listAction() {
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */

		$dataSource = new ArrayDataSource($this->loadData(), 'id');

		$grid = $gridFactory->create('domainsList', $dataSource);

		$grid->addColumn('name', 'name', 'Název domény');
		$grid->addColumn('locale', 'locale', 'Jazyk');

		$grid->setTheme('@SS6Shop/Admin/Content/Overview/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Overview/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	private function loadData() {
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$domainConfigs = $domain->getAll();
		$data = [];
		foreach ($domainConfigs as $id => $domainDetail) {
			$data[$id]['id'] = $id;
			$data[$id]['name'] = $domainDetail->getDomain();
			$data[$id]['locale'] =  $domainDetail->getLocale();
		}

		return $data;
	}
}
