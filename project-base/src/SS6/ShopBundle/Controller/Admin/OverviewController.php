<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Grid\GridFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OverviewController extends Controller {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	public function __construct(
		GridFactory $gridFactory,
		Domain $domain
	) {
		$this->gridFactory = $gridFactory;
		$this->domain = $domain;
	}

	/**
	 * @Route("/overview/")
	 */
	public function listAction() {
		$dataSource = new ArrayDataSource($this->loadData(), 'id');

		$grid = $this->gridFactory->create('domainsList', $dataSource);

		$grid->addColumn('name', 'name', 'Název domény');
		$grid->addColumn('locale', 'locale', 'Jazyk');

		$grid->setTheme('@SS6Shop/Admin/Content/Overview/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Overview/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	private function loadData() {
		$data = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$data[] = [
				'id' => $domainConfig->getId(),
				'name' => $domainConfig->getName(),
				'locale' => $domainConfig->getLocale(),
			];
		}

		return $data;
	}
}
