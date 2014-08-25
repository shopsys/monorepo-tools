<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\PKGrid\GridFactory;

class AvailabilityGridFactory {

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
	function __construct(EntityManager $em, GridFactory $gridFactory) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function get() {

		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Availability::class, 'a');

		$grid = $this->gridFactory->get('availabilityList');
		$grid->setInlineEditService(
			'ss6.shop.product.availability.inline_edit',
			'a.id'
		);
		$grid->setDefaultOrder('name');
		$grid->setQueryBuilder($queryBuilder);

		$grid->addColumn('name', 'a.name', 'Název', true);

		$grid->setActionColumnClass('table-col table-col-10');
		$grid->addActionColumn('delete', 'Smazat', 'admin_availability_delete', array('id' => 'a.id'))
			->setConfirmMessage('Opravdu chcete odstranit tuto dostupnost?');

		return $grid;
	}
}
