<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Product\Product;

class TopProductGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Grid\GridFactory $gridFactory
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('tp, p')
			->from(TopProduct::class, 'tp')
			->join(Product::class, 'p', 'WITH', 'tp.product = p.id')
			->where('tp.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'tp.id');

		$grid = $this->gridFactory->create('topProductsList', $dataSource);
		$grid->addColumn('product', 'p.id', 'Produkt');
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('delete', 'Smazat', 'admin_topproducts_delete', array('id' => 'tp.id'))
			->setConfirmMessage('Opravdu chcete odstranit tento produkt?');

		return $grid;
	}
}