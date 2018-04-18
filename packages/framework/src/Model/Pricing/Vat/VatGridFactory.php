<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;

class VatGridFactory implements GridFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation
     */
    private $priceCalculation;

    public function __construct(
        EntityManagerInterface $em,
        GridFactory $gridFactory,
        VatFacade $vatFacade,
        PriceCalculation $priceCalculation
    ) {
        $this->em = $em;
        $this->gridFactory = $gridFactory;
        $this->vatFacade = $vatFacade;
        $this->priceCalculation = $priceCalculation;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('v, COUNT(rv.id) as asReplacementCount')
            ->from(Vat::class, 'v')
            ->leftJoin(Vat::class, 'rv', Join::WITH, 'v.id = rv.replaceWith')
            ->groupBy('v');
        $dataSource = new QueryBuilderWithRowManipulatorDataSource($queryBuilder, 'v.id', function ($row) {
            $vat = $this->vatFacade->getById($row['v']['id']);
            $row['vat'] = $vat;
            $row['coefficient'] = $this->priceCalculation->getVatCoefficientByPercent($vat->getPercent());

            return $row;
        });

        $grid = $this->gridFactory->create('vatList', $dataSource);
        $grid->setDefaultOrder('name');
        $grid->addColumn('name', 'v.name', t('Name'), true);
        $grid->addColumn('percent', 'v.percent', t('Percent'), true);
        $grid->addColumn('coefficient', 'v.percent', t('Coefficient'), true);
        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addDeleteActionColumn('admin_vat_deleteconfirm', ['id' => 'v.id'])
            ->setAjaxConfirm();

        $grid->setTheme('@ShopsysFramework/Admin/Content/Vat/listGrid.html.twig');

        return $grid;
    }
}
