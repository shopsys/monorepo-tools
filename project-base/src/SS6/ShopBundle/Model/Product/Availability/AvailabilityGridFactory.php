<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use \SS6\ShopBundle\Model\PKGrid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\PKGrid\GridFactoryInterface;
use SS6\ShopBundle\Model\PKGrid\PKGridFactory;

class AvailabilityGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\PKGrid\PKGridFactory
	 */
	private $gridFactory;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\PKGrid\PKGridFactory $gridFactory
	 */
	public function __construct(EntityManager $em, PKGridFactory $gridFactory) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Availability::class, 'a');
		$dataSource = new QueryBuilderDataSource($queryBuilder);

		$grid = $this->gridFactory->create('availabilityList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'a.name', 'Název', true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('delete', 'Smazat', 'admin_availability_delete', array('id' => 'a.id'))
			->setConfirmMessage('Opravdu chcete odstranit tuto dostupnost?');

		return $grid;
	}
}
