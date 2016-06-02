<?php

namespace SS6\ShopBundle\Model\Product\Flag;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Localization\Localization;

class FlagGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('a, at')
			->from(Flag::class, 'a')
			->join('a.translations', 'at', Join::WITH, 'at.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$grid = $this->gridFactory->create('flagList', $dataSource);
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'at.name', t('Název'), true);
		$grid->addColumn('rgbColor', 'a.rgbColor', t('Barva'), true);
		$grid->addColumn('visible', 'a.visible', t('Filtrovat podle'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addDeleteActionColumn('admin_flag_delete', ['id' => 'a.id'])
			->setConfirmMessage(t('Opravdu chcete odstranit tento příznak?'));

		$grid->setTheme('@SS6Shop/Admin/Content/Flag/listGrid.html.twig');

		return $grid;
	}
}
