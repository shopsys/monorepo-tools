<?php

namespace SS6\ShopBundle\Model\Department\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Department\Department;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;

class DepartmentGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Grid\GridFactory $gridFactory
	 */
	public function __construct(EntityManager $em, GridFactory $gridFactory) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('d')
			->from(Department::class, 'd');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'd.id');

		$grid = $this->gridFactory->create('departmentList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'd.name', 'Název', true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(ActionColumn::TYPE_DELETE, 'Smazat', 'admin_department_delete', array('id' => 'd.id'))
			->setConfirmMessage('Opravdu chcete smazat toto oddělení?');

		return $grid;
	}
}
