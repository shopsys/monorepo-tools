<?php

namespace SS6\ShopBundle\Model\Pricing\Group\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;

class PricingGroupGridFactory implements GridFactoryInterface {

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
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		Translator $translator
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('pg')
			->from(PricingGroup::class, 'pg')
			->where('pg.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'pg.id');

		$grid = $this->gridFactory->create('pricingGroupList', $dataSource);
		$grid->setDefaultOrder('name');
		$grid->addColumn('name', 'pg.name', $this->translator->trans('Název'), true);
		$grid->addColumn('coefficient', 'pg.coefficient', $this->translator->trans('Koeficient'), true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				ActionColumn::TYPE_DELETE,
				$this->translator->trans('Smazat'),
				'admin_pricinggroup_deleteconfirm',
				['id' => 'pg.id']
			)
			->setAjaxConfirm();

		$grid->setTheme('@SS6Shop/Admin/Content/Pricing/Groups/listGrid.html.twig');

		return $grid;
	}
}
