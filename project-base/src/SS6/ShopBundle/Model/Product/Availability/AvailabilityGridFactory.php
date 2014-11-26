<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Localization\Localization;

class AvailabilityGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(EntityManager $em, GridFactory $gridFactory, Localization $localization) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
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
		$grid->setDefaultOrder('name');

		$grid->addColumn('names', 'at.name', 'Název', true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('delete', 'Smazat', 'admin_availability_delete', array('id' => 'a.id'))
			->setConfirmMessage('Opravdu chcete odstranit tuto dostupnost?');

		return $grid;
	}
}
