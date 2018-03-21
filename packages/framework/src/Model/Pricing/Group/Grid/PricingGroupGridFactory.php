<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;

class PricingGroupGridFactory implements GridFactoryInterface
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
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        EntityManagerInterface $em,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->em = $em;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('pg')
            ->from(PricingGroup::class, 'pg')
            ->where('pg.domainId = :selectedDomainId')
            ->setParameter('selectedDomainId', $this->adminDomainTabsFacade->getSelectedDomainId());
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'pg.id');

        $grid = $this->gridFactory->create('pricingGroupList', $dataSource);
        $grid->setDefaultOrder('name');
        $grid->addColumn('name', 'pg.name', t('Name'), true);
        $grid->addColumn('coefficient', 'pg.coefficient', t('Coefficient'), true);
        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addDeleteActionColumn('admin_pricinggroup_deleteconfirm', ['id' => 'pg.id'])
            ->setAjaxConfirm();

        $grid->setTheme('@ShopsysFramework/Admin/Content/Pricing/Groups/listGrid.html.twig');

        return $grid;
    }
}
