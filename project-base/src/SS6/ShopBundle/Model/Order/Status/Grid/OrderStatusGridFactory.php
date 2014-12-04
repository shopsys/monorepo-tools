<?php

namespace SS6\ShopBundle\Model\Order\Status\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Component\Translation\TranslatorInterface;

class OrderStatusGridFactory implements GridFactoryInterface {

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

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		Localization $localization,
		TranslatorInterface $translator
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->localization = $localization;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('os, ost')
			->from(OrderStatus::class, 'os')
			->join('os.translations', 'ost', Join::WITH, 'ost.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'os.id');

		$grid = $this->gridFactory->create('orderStatusList', $dataSource);
		$grid->setDefaultOrder('name');

		$grid->addColumn('names', 'ost.name', $this->translator->trans('Název'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				ActionColumn::TYPE_DELETE,
				$this->translator->trans('Smazat'),
				'admin_orderstatus_deleteconfirm',
				array('id' => 'os.id')
			)
			->setAjaxConfirm();

		return $grid;
	}

}
