<?php

namespace SS6\ShopBundle\Model\Payment\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Payment\Detail\PaymentDetailFactory;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentRepository;

class PaymentGridFactory implements GridFactoryInterface {

	const CURRENCY_ID_FOR_LIST = 1;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Detail\PaymentDetailFactory
	 */
	private $paymentDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		PaymentRepository $paymentRepository,
		PaymentDetailFactory $paymentDetailFactory,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->paymentRepository = $paymentRepository;
		$this->paymentDetailFactory = $paymentDetailFactory;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
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

		$grid->addColumn('name', 'pt.name', t('Name'));
		$grid->addColumn('price', 'paymentDetail', t('Price'));

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_payment_edit', ['id' => 'p.id']);
		$grid->addDeleteActionColumn('admin_payment_delete', ['id' => 'p.id'])
			->setConfirmMessage(t('Do you really want to remove this payment?'));

		$grid->setTheme(
			'@SS6Shop/Admin/Content/Payment/listGrid.html.twig',
			['currencyIdForList' => self::CURRENCY_ID_FOR_LIST]
		);

		return $grid;
	}
}
