<?php

namespace SS6\ShopBundle\Model\Transport\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Transport\Detail\Factory;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class TransportGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Detail\Factory
	 */
	private $transportDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @param \Doctrine\ORM\EntityManager\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Grid\GridFactory $gridFactory
	 * @param \SS6\ShopBundle\Model\Transport\TransportRepository $transportRepository
	 * @param \SS6\ShopBundle\Model\Transport\Detail\Factory $transportDetailFactory
	 * @param \SS6\ShopBundle\Model\Localization\Localization $localization
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		TransportRepository $transportRepository,
		Factory $transportDetailFactory,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->transportRepository = $transportRepository;
		$this->transportDetailFactory = $transportDetailFactory;
		$this->localization = $localization;
	}

	/**
	 * @return Grid
	 */
	public function create() {
		$queryBuilder = $this->transportRepository->getQueryBuilderForAll()
			->addSelect('tt')
			->join('t.translations', 'tt', Join::WITH, 'tt.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderWithRowManipulatorDataSource(
			$queryBuilder, 't.id',
			function ($row) {
				$transport = $this->transportRepository->findById($row['t']['id']);
				$row['transportDetail'] = $this->transportDetailFactory->createDetailForTransport($transport);
				return $row;
			}
		);

		$grid = $this->gridFactory->create('transportList', $dataSource);
		$grid->enableDragAndDrop(Transport::class);

		$grid->addColumn('name', 'tt.name', 'Název');
		$grid->addColumn('price', 'transportDetail', 'Cena');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(ActionColumn::TYPE_EDIT, 'Upravit', 'admin_transport_edit', array('id' => 't.id'));
		$grid->addActionColumn(ActionColumn::TYPE_DELETE, 'Smazat', 'admin_transport_delete', array('id' => 't.id'))
			->setConfirmMessage('Opravdu chcete odstranit tuto dopravu?');

		return $grid;
	}
}
