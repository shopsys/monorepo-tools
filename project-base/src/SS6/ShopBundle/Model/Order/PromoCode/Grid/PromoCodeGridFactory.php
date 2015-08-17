<?php

namespace SS6\ShopBundle\Model\Order\PromoCode\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCode;

class PromoCodeGridFactory implements GridFactoryInterface {

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

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		Translator $translator
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('pc')
			->from(PromoCode::class, 'pc');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'pc.id');

		$grid = $this->gridFactory->create('promoCodeList', $dataSource);
		$grid->setDefaultOrder('code');
		$grid->addColumn('code', 'pc.code', $this->translator->trans('Kód'), true);
		$grid->addColumn('percent', 'pc.percent', $this->translator->trans('Sleva'), true);
		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(
				'delete',
				$this->translator->trans('Smazat'),
				'admin_promocode_delete',
				['id' => 'pc.id']
			)
			->setConfirmMessage($this->translator->trans('Opravdu chcete odstranit tento slevový kupón?'));

		$grid->setTheme('@SS6Shop/Admin/Content/PromoCode/listGrid.html.twig');

		return $grid;
	}
}
