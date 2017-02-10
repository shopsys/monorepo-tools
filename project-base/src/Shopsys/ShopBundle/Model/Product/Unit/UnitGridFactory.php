<?php

namespace Shopsys\ShopBundle\Model\Product\Unit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Model\Localization\Localization;

class UnitGridFactory implements GridFactoryInterface
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
            ->select('u, ut')
            ->from(Unit::class, 'u')
            ->join('u.translations', 'ut', Join::WITH, 'ut.locale = :locale')
            ->setParameter('locale', $this->localization->getDefaultLocale());
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'u.id');

        $grid = $this->gridFactory->create('unitList', $dataSource);
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'ut.name', t('Name'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addDeleteActionColumn('admin_unit_deleteconfirm', ['id' => 'u.id'])
            ->setAjaxConfirm();

        $grid->setTheme('@ShopsysShop/Admin/Content/Unit/listGrid.html.twig');

        return $grid;
    }
}
