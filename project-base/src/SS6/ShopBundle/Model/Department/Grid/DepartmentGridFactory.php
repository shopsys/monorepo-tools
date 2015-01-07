<?php

namespace SS6\ShopBundle\Model\Category\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Component\Translation\TranslatorInterface;

class CategoryGridFactory implements GridFactoryInterface {

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
			->select('d, dt')
			->from(Category::class, 'd')
			->join('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
			->orderBy('d.root, d.lft', 'ASC')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'd.id');

		$grid = $this->gridFactory->create('categoryList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('names', 'dt.name', $this->translator->trans('Název'), true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				ActionColumn::TYPE_EDIT,
				$this->translator->trans('Upravit'),
				'admin_category_edit',
				array('id' => 'd.id')
			);
		$grid->addActionColumn(
				ActionColumn::TYPE_DELETE,
				$this->translator->trans('Smazat'),
				'admin_category_delete',
				array('id' => 'd.id')
			)
			->setConfirmMessage($this->translator->trans('Opravdu chcete smazat tuto kategorii?'));

		$grid->setTheme('@SS6Shop/Admin/Content/Category/listGrid.html.twig');

		return $grid;
	}
}
