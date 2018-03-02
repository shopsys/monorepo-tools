<?php

namespace Shopsys\FrameworkBundle\Model\Payment\Grid;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Payment\Detail\PaymentDetailFactory;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentRepository;

class PaymentGridFactory implements GridFactoryInterface
{
    const CURRENCY_ID_FOR_LIST = 1;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Detail\PaymentDetailFactory
     */
    private $paymentDetailFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(
        GridFactory $gridFactory,
        PaymentRepository $paymentRepository,
        PaymentDetailFactory $paymentDetailFactory,
        Localization $localization
    ) {
        $this->gridFactory = $gridFactory;
        $this->paymentRepository = $paymentRepository;
        $this->paymentDetailFactory = $paymentDetailFactory;
        $this->localization = $localization;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create()
    {
        $queryBuilder = $this->paymentRepository->getQueryBuilderForAll()
            ->addSelect('pt')
            ->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->setParameter('locale', $this->localization->getAdminLocale());
        $dataSource = new QueryBuilderWithRowManipulatorDataSource(
            $queryBuilder,
            'p.id',
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
            '@ShopsysFramework/Admin/Content/Payment/listGrid.html.twig',
            ['currencyIdForList' => self::CURRENCY_ID_FOR_LIST]
        );

        return $grid;
    }
}
