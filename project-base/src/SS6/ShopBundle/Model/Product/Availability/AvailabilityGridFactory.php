<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Localization\Localization;

class AvailabilityGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('a, at')
			->from(Availability::class, 'a')
			->join('a.translations', 'at', Join::WITH, 'at.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$grid = $this->gridFactory->create('availabilityList', $dataSource);
		$grid->setDefaultOrder('dispatchTime');

		$grid->addColumn('name', 'at.name', t('Name'), true);
		$grid->addColumn('dispatchTime', 'a.dispatchTime', t('Number of days to despatch'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addDeleteActionColumn('admin_availability_deleteconfirm', ['id' => 'a.id'])
			->setAjaxConfirm();

		$grid->setTheme('@SS6Shop/Admin/Content/Availability/listGrid.html.twig');

		return $grid;
	}
}
