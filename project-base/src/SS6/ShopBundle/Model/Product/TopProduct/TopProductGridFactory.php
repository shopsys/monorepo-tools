<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Localization\Localization;

class TopProductGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('tp, pt, p.id')
			->from(TopProduct::class, 'tp')
			->join('tp.product', 'p')
			->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
			->where('tp.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId())
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'tp.id');

		$grid = $this->gridFactory->create('topProductList', $dataSource);
		$grid->addColumn('product', 'pt.name', t('Produkt'));
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				'delete',
				t('Smazat'),
				'admin_topproduct_delete',
				['id' => 'tp.id']
			)
			->setConfirmMessage(t('Opravdu chcete odebrat tento produkt z akce na titulní stránce?'));

		$grid->setTheme('@SS6Shop/Admin/Content/TopProduct/listGrid.html.twig');

		return $grid;
	}
}
