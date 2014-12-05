<?php

namespace SS6\ShopBundle\Model\Pricing\Currency\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;

class CurrencyGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;


	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Grid\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Grid\GridFactory $gridFactory
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('c')
			->from(Currency::class, 'c');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'c.id');

		$grid = $this->gridFactory->create('currencyList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'c.name', 'Název', true);
		$grid->addColumn('code', 'c.code', 'Kód', true);
		$grid->addColumn('symbol', 'c.symbol', 'Symbol', true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(ActionColumn::TYPE_DELETE, 'Smazat', 'admin_currency_deleteconfirm', array('id' => 'c.id'))
			->setAjaxConfirm();

		return $grid;
	}
}
