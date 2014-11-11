<?php

namespace SS6\ShopBundle\Model\Payment\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Grid\ActionColumn;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Payment\Detail\Factory;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentRepository;

class PaymentGridFactory implements GridFactoryInterface {

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
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Detail\Factory
	 */
	private $paymentDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @param \Doctrine\ORM\EntityManager\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Grid\GridFactory $gridFactory
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 * @param \SS6\ShopBundle\Model\Payment\Detail\Factory $paymentDetailFactory
	 * @param \SS6\ShopBundle\Model\Localization\Localization $localization
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		PaymentRepository $paymentRepository,
		Factory $paymentDetailFactory,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->paymentRepository = $paymentRepository;
		$this->paymentDetailFactory = $paymentDetailFactory;
		$this->localization = $localization;
	}

	/**
	 * @return Grid
	 */
	public function create() {
		$queryBuilder = $this->paymentRepository->getQueryBuilderForAll()
			->addSelect('pt')
			->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$dataSource = new QueryBuilderWithRowManipulatorDataSource(
			$queryBuilder, 'p.id',
			function ($row) {
				$payment = $this->paymentRepository->findById($row['p']['id']);
				$row['paymentDetail'] = $this->paymentDetailFactory->createDetailForPayment($payment);
				return $row;
			}
		);

		$grid = $this->gridFactory->create('paymentList', $dataSource);
		$grid->enableDragAndDrop(Payment::class);

		$grid->addColumn('name', 'pt.name', 'Název');
		$grid->addColumn('price', 'paymentDetail', 'Cena');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn(ActionColumn::TYPE_EDIT, 'Upravit', 'admin_payment_edit', array('id' => 'p.id'));
		$grid->addActionColumn(ActionColumn::TYPE_DELETE, 'Smazat', 'admin_payment_delete', array('id' => 'p.id'))
			->setConfirmMessage('Opravdu chcete odstranit tuto platbu?');

		return $grid;
	}
}
