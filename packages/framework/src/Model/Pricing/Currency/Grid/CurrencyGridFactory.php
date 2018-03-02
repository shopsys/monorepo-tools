<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency\Grid;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;

class CurrencyGridFactory implements GridFactoryInterface
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
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\FrameworkBundle\Component\Grid\GridFactory $gridFactory
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     */
    public function __construct(
        EntityManager $em,
        GridFactory $gridFactory,
        CurrencyFacade $currencyFacade
    ) {
        $this->em = $em;
        $this->gridFactory = $gridFactory;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('c')
            ->from(Currency::class, 'c');
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'c.id');

        $grid = $this->gridFactory->create('currencyList', $dataSource);
        $grid->setDefaultOrder('name');
        $grid->addColumn('name', 'c.name', t('Name'), true);
        $grid->addColumn('code', 'c.code', t('Code'), true);
        $grid->addColumn('exchangeRate', 'c.exchangeRate', t('Exchange rate'), true);
        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addDeleteActionColumn('admin_currency_deleteconfirm', ['id' => 'c.id'])
            ->setAjaxConfirm();

        $grid->setTheme(
            '@ShopsysFramework/Admin/Content/Currency/listGrid.html.twig',
            [
                'defaultCurrency' => $this->currencyFacade->getDefaultCurrency(),
                'notAllowedToDeleteCurrencyIds' => $this->currencyFacade->getNotAllowedToDeleteCurrencyIds(),
            ]
        );

        return $grid;
    }
}
