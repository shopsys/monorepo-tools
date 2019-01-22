<?php

namespace Shopsys\FrameworkBundle\Model\Country\Grid;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Grid\Grid;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\GridFactoryInterface;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Model\Country\CountryRepository;
use Shopsys\FrameworkBundle\Model\Localization\Localization;

class CountryGridFactory implements GridFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    protected $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    protected $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryRepository
     */
    protected $countryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryRepository $countryRepository
     * @param \Shopsys\FrameworkBundle\Component\Grid\GridFactory $gridFactory
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        CountryRepository $countryRepository,
        GridFactory $gridFactory,
        Localization $localization,
        Domain $domain
    ) {
        $this->gridFactory = $gridFactory;
        $this->localization = $localization;
        $this->countryRepository = $countryRepository;
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    public function create(): Grid
    {
        $queryBuilder = $this->countryRepository
            ->createSortedJoinedQueryBuilder($this->localization->getAdminLocale(), $this->domain->getId())
            ->addSelect('ct');

        $dataSource = new QueryBuilderDataSource($queryBuilder, 'c.id');

        $grid = $this->gridFactory->create('CountryList', $dataSource);

        $grid->addColumn('name', 'ct.name', t('Name'), true);
        $grid->addColumn('code', 'c.code', t('Country code'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_country_edit', ['id' => 'c.id']);

        $grid->setTheme('@ShopsysFramework/Admin/Content/Country/listGrid.html.twig');

        return $grid;
    }
}
