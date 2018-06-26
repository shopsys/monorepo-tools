<?php

namespace Shopsys\FrameworkBundle\Model\Transport\Grid;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportRepository;

class TransportGridFactory implements GridFactoryInterface
{
    const CURRENCY_ID_FOR_LIST = 1;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    public function __construct(
        GridFactory $gridFactory,
        TransportRepository $transportRepository,
        Localization $localization,
        TransportFacade $transportFacade
    ) {
        $this->gridFactory = $gridFactory;
        $this->transportRepository = $transportRepository;
        $this->localization = $localization;
        $this->transportFacade = $transportFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
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
                $row['displayPrice'] = $this->getDisplayPrice($transport);
                return $row;
            }
        );

        $grid = $this->gridFactory->create('transportList', $dataSource);
        $grid->enableDragAndDrop(Transport::class);

        $grid->addColumn('name', 'tt.name', t('Name'));
        $grid->addColumn('price', 'displayPrice', t('Price'));

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_transport_edit', ['id' => 't.id']);
        $grid->addDeleteActionColumn('admin_transport_delete', ['id' => 't.id'])
            ->setConfirmMessage(t('Do you really want to remove this shipping?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Transport/listGrid.html.twig');

        return $grid;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return string
     */
    private function getDisplayPrice(Transport $transport)
    {
        $transportBasePricesIndexedByCurrencyId = $this->transportFacade->getIndependentBasePricesIndexedByCurrencyId($transport);

        return $transportBasePricesIndexedByCurrencyId[self::CURRENCY_ID_FOR_LIST]->getPriceWithVat();
    }
}
