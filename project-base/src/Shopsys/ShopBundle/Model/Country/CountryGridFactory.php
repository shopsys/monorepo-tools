<?php

namespace SS6\ShopBundle\Model\Country;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;

class CountryGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

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
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('s')
			->from(Country::class, 's')
			->andWhere('s.domainId = :domainId')
			->setParameter('domainId', $this->selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 's.id');

		$grid = $this->gridFactory->create('CountryList', $dataSource);
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 's.name', t('Name'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->setTheme('@SS6Shop/Admin/Content/Country/listGrid.html.twig');

		return $grid;
	}
}
