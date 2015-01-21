<?php

namespace SS6\ShopBundle\Model\Product\Flag;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Translation\TranslatorInterface;

class FlagGridFactory implements GridFactoryInterface {

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
			->select('a, at')
			->from(Flag::class, 'a')
			->join('a.translations', 'at', Join::WITH, 'at.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$grid = $this->gridFactory->create('flagList', $dataSource);
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'at.name', $this->translator->trans('Název'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				'delete',
				$this->translator->trans('Smazat'),
				'admin_flag_delete',
				['id' => 'a.id']
			)
			->setConfirmMessage($this->translator->trans('Opravdu chcete odstranit tento příznak?'));

		$grid->setTheme('@SS6Shop/Admin/Content/Flag/listGrid.html.twig');

		return $grid;
	}
}
