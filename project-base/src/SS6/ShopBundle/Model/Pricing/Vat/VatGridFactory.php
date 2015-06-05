<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;

class VatGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		Translator $translator,
		VatFacade $vatFacade
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->translator = $translator;
		$this->vatFacade = $vatFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('v, COUNT(rv.id) as asReplacementCount')
			->from(Vat::class, 'v')
			->leftJoin(Vat::class, 'rv', Join::WITH, 'v.id = rv.replaceWith')
			->groupBy('v');
		$dataSource = new QueryBuilderWithRowManipulatorDataSource($queryBuilder, 'v.id', function ($row) {
			$row['vat'] = $this->vatFacade->getById($row['v']['id']);

			return $row;
		});

		$grid = $this->gridFactory->create('vatList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'v.name', $this->translator->trans('Název'), true);
		$grid->addColumn('percent', 'v.percent', $this->translator->trans('Procent'), true);
		$grid->addColumn('coefficient', 'v.percent', $this->translator->trans('Koeficient'), true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				ActionColumn::TYPE_DELETE,
				$this->translator->trans('Smazat'),
				'admin_vat_deleteconfirm',
				['id' => 'v.id']
			)
			->setAjaxConfirm();

		$grid->setTheme('@SS6Shop/Admin/Content/Vat/listGrid.html.twig');

		return $grid;
	}
}
