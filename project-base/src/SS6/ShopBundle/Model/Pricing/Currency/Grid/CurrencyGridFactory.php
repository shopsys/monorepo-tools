<?php

namespace SS6\ShopBundle\Model\Pricing\Currency\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;

class CurrencyGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Component\Grid\GridFactory $gridFactory
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		CurrencyFacade $currencyFacade
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->currencyFacade = $currencyFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('c')
			->from(Currency::class, 'c');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'c.id');

		$grid = $this->gridFactory->create('currencyList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'c.name', t('Name'), true);
		$grid->addColumn('code', 'c.code', t('Code'), true);
		$grid->addColumn('exchangeRate', 'c.exchangeRate', t('Exchange rate'), true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addDeleteActionColumn('admin_currency_deleteconfirm', ['id' => 'c.id'])
			->setAjaxConfirm();

		$grid->setTheme(
			'@SS6Shop/Admin/Content/Currency/listGrid.html.twig',
			[
				'defaultCurrency' => $this->currencyFacade->getDefaultCurrency(),
				'notAllowedToDeleteCurrencyIds' => $this->currencyFacade->getNotAllowedToDeleteCurrencyIds(),
			]
		);

		return $grid;
	}
}
