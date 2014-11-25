<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductTranslation;

class TopProductGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
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
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('tp, pt')
			->from(TopProduct::class, 'tp')
			->join('tp.product', 'p')
			->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
			->where('tp.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId())
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'tp.id');

		$grid = $this->gridFactory->create('topProductList', $dataSource);
		$grid->addColumn('product', 'pt.name', 'Produkt');
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('delete', 'Smazat', 'admin_topproduct_delete', array('id' => 'tp.id'))
			->setConfirmMessage('Opravdu chcete odebrat tento produkt z akce na titulní stránce?');

		return $grid;
	}
}