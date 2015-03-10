<?php

namespace SS6\ShopBundle\Model\Transport\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Transport\Detail\TransportDetailFactory;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class TransportGridFactory implements GridFactoryInterface {

	const CURRENCY_ID_FOR_LIST = 1;

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
	 * @var \SS6\ShopBundle\Model\Transport\Detail\TransportDetailFactory
	 */
	private $transportDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		TransportRepository $transportRepository,
		TransportDetailFactory $transportDetailFactory,
		Localization $localization,
		Translator $translator
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->transportRepository = $transportRepository;
		$this->transportDetailFactory = $transportDetailFactory;
		$this->localization = $localization;
		$this->translator = $translator;
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
				$row['transportDetail'] = $this->transportDetailFactory->createDetailForTransportWithIndependentPrices($transport);
				return $row;
			}
		);

		$grid = $this->gridFactory->create('transportList', $dataSource);
		$grid->enableDragAndDrop(Transport::class);

		$grid->addColumn('name', 'tt.name', $this->translator->trans('Název'));
		$grid->addColumn('price', 'transportDetail', $this->translator->trans('Cena'));

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
			ActionColumn::TYPE_EDIT,
			$this->translator->trans('Upravit'),
			'admin_transport_edit',
			['id' => 't.id']
		);
		$grid->addActionColumn(
				ActionColumn::TYPE_DELETE,
				$this->translator->trans('Smazat'),
				'admin_transport_delete',
				['id' => 't.id']
			)
			->setConfirmMessage($this->translator->trans('Opravdu chcete odstranit tuto dopravu?'));

		$grid->setTheme(
			'@SS6Shop/Admin/Content/Transport/listGrid.html.twig',
			['currencyIdForList' => self::CURRENCY_ID_FOR_LIST]
		);

		return $grid;
	}
}
