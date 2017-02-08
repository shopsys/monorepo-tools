<?php

namespace SS6\ShopBundle\Model\Order\PromoCode\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCode;

class PromoCodeGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('pc')
			->from(PromoCode::class, 'pc');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'pc.id');

		$grid = $this->gridFactory->create('promoCodeList', $dataSource);
		$grid->setDefaultOrder('code');
		$grid->addColumn('code', 'pc.code', t('Code'), true);
		$grid->addColumn('percent', 'pc.percent', t('Discount'), true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addDeleteActionColumn('admin_promocode_delete', ['id' => 'pc.id'])
			->setConfirmMessage(t('Do you really want to remove this discount coupon?'));

		$grid->setTheme('@SS6Shop/Admin/Content/PromoCode/listGrid.html.twig');

		return $grid;
	}
}
