<?php

namespace Shopsys\ShopBundle\Model\Transport\Grid;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Transport\Detail\TransportDetailFactory;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportRepository;

class TransportGridFactory implements GridFactoryInterface
{
    const CURRENCY_ID_FOR_LIST = 1;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Detail\TransportDetailFactory
     */
    private $transportDetailFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(
        GridFactory $gridFactory,
        TransportRepository $transportRepository,
        TransportDetailFactory $transportDetailFactory,
        Localization $localization
    ) {
        $this->gridFactory = $gridFactory;
        $this->transportRepository = $transportRepository;
        $this->transportDetailFactory = $transportDetailFactory;
        $this->localization = $localization;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
     */
    public function create()
    {
        $queryBuilder = $this->transportRepository->getQueryBuilderForAll()
            ->addSelect('tt')
            ->join('t.translations', 'tt', Join::WITH, 'tt.locale = :locale')
            ->setParameter('locale', $this->localization->getAdminLocale());
        $dataSource = new QueryBuilderWithRowManipulatorDataSource(
            $queryBuilder,
            't.id',
            function ($row) {
                $transport = $this->transportRepository->findById($row['t']['id']);
                $row['transportDetail'] = $this->transportDetailFactory->createDetailForTransportWithIndependentPrices($transport);
                return $row;
            }
        );

        $grid = $this->gridFactory->create('transportList', $dataSource);
        $grid->enableDragAndDrop(Transport::class);

        $grid->addColumn('name', 'tt.name', t('Name'));
        $grid->addColumn('price', 'transportDetail', t('Price'));

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_transport_edit', ['id' => 't.id']);
        $grid->addDeleteActionColumn('admin_transport_delete', ['id' => 't.id'])
            ->setConfirmMessage(t('Do you really want to remove this shipping?'));

        $grid->setTheme(
            '@ShopsysShop/Admin/Content/Transport/listGrid.html.twig',
            ['currencyIdForList' => self::CURRENCY_ID_FOR_LIST]
        );

        return $grid;
    }
}
