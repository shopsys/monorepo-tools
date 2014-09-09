<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\PKGrid\ActionColumn;
use SS6\ShopBundle\Model\PKGrid\PKGridFactory;
use SS6\ShopBundle\Model\PKGrid\GridFactoryInterface;
use SS6\ShopBundle\Model\PKGrid\QueryBuilderDataSource;

class ParameterGridFactory implements GridFactoryInterface {

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
			->select('p')
			->from(Parameter::class, 'p');
		$dataSource = new QueryBuilderDataSource($queryBuilder);

		$grid = $this->gridFactory->create('parameterList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'p.name', 'Název', true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(ActionColumn::TYPE_DELETE, 'Smazat', 'admin_parameter_delete', array('id' => 'p.id'))
			->setConfirmMessage('Opravdu chcete odstranit tento parametr?');

		return $grid;
	}
}
