<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\PKGrid\ActionColumn;
use SS6\ShopBundle\Model\PKGrid\GridFactory;
use SS6\ShopBundle\Model\PKGrid\QueryBuilderDataSource;

class ParameterGridFactory {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\PKGrid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\PKGrid\GridFactory $gridFactory
	 */
	public function __construct(EntityManager $em, GridFactory $gridFactory) {
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
			->from(Parameter::class, 'a');
		$dataSource = new QueryBuilderDataSource($queryBuilder);

		$grid = $this->gridFactory->create('parameterList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'a.name', 'Název', true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(ActionColumn::TYPE_DELETE, 'Smazat', 'admin_parameter_delete', array('id' => 'a.id'))
			->setConfirmMessage('Opravdu chcete odstranit tento parametr?');

		return $grid;
	}
}
