<?php

namespace SS6\ShopBundle\Model\Order\Status\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusGridFactory implements GridFactoryInterface {

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
			->select('os, ost')
			->from(OrderStatus::class, 'os')
			->join('os.translations', 'ost', Join::WITH, 'ost.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'os.id');

		$grid = $this->gridFactory->create('orderStatusList', $dataSource);
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'ost.name', t('Name'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addDeleteActionColumn('admin_orderstatus_deleteconfirm', ['id' => 'os.id'])
			->setAjaxConfirm();

		$grid->setTheme('@SS6Shop/Admin/Content/OrderStatus/listGrid.html.twig', [
			'TYPE_NEW' => OrderStatus::TYPE_NEW,
			'TYPE_DONE' => OrderStatus::TYPE_DONE,
			'TYPE_CANCELED' => OrderStatus::TYPE_CANCELED,
		]);

		return $grid;
	}

}
