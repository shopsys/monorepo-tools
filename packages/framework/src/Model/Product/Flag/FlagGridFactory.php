<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Model\Localization\Localization;

class FlagGridFactory implements GridFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
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
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('a, at')
            ->from(Flag::class, 'a')
            ->join('a.translations', 'at', Join::WITH, 'at.locale = :locale')
            ->setParameter('locale', $this->localization->getAdminLocale());
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

        $grid = $this->gridFactory->create('flagList', $dataSource);
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'at.name', t('Name'), true);
        $grid->addColumn('rgbColor', 'a.rgbColor', t('Colour'), true);
        $grid->addColumn('visible', 'a.visible', t('Filter by'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addDeleteActionColumn('admin_flag_delete', ['id' => 'a.id'])
            ->setConfirmMessage(t('Do you really want to remove this flag?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Flag/listGrid.html.twig');

        return $grid;
    }
}
