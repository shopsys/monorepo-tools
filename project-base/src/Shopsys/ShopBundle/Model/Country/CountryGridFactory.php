<?php

namespace Shopsys\ShopBundle\Model\Country;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;

class CountryGridFactory implements GridFactoryInterface
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
            ->select('s')
            ->from(Country::class, 's')
            ->andWhere('s.domainId = :domainId')
            ->setParameter('domainId', $this->adminDomainTabsFacade->getId());
        $dataSource = new QueryBuilderDataSource($queryBuilder, 's.id');

        $grid = $this->gridFactory->create('CountryList', $dataSource);
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 's.name', t('Name'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->setTheme('@ShopsysShop/Admin/Content/Country/listGrid.html.twig');

        return $grid;
    }
}
