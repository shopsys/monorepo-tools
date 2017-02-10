<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusGridFactory implements GridFactoryInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
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
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
     */
    public function create() {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('os, ost')
            ->from(OrderStatus::class, 'os')
            ->join('os.translations', 'ost', Join::WITH, 'ost.locale = :locale')
            ->setParameter('locale', $this->localization->getDefaultLocale());
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'os.id');

        $grid = $this->gridFactory->create('orderStatusList', $dataSource);
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'ost.name', t('Name'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addDeleteActionColumn('admin_orderstatus_deleteconfirm', ['id' => 'os.id'])
            ->setAjaxConfirm();

        $grid->setTheme('@ShopsysShop/Admin/Content/OrderStatus/listGrid.html.twig', [
            'TYPE_NEW' => OrderStatus::TYPE_NEW,
            'TYPE_DONE' => OrderStatus::TYPE_DONE,
            'TYPE_CANCELED' => OrderStatus::TYPE_CANCELED,
        ]);

        return $grid;
    }
}
