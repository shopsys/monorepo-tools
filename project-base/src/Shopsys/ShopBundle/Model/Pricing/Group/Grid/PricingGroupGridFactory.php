<?php

namespace Shopsys\ShopBundle\Model\Pricing\Group\Grid;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;

class PricingGroupGridFactory implements GridFactoryInterface
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
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        EntityManager $em,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->em = $em;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
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

        $grid->setTheme('@ShopsysShop/Admin/Content/Pricing/Groups/listGrid.html.twig');

        return $grid;
    }
}
