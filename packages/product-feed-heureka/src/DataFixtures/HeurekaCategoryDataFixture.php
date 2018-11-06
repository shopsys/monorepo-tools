<?php

namespace Shopsys\ProductFeed\HeurekaBundle\DataFixtures;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDataFactoryInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;

class HeurekaCategoryDataFixture implements PluginDataFixtureInterface
{
    const HEUREKA_CATEGORY_ID_FIRST = 1;
    const HEUREKA_CATEGORY_ID_SECOND = 2;
    const HEUREKA_CATEGORY_ID_THIRD = 3;

    const CATEGORY_ID_FIRST = 2;
    const CATEGORY_ID_SECOND = 3;
    const CATEGORY_ID_THIRD = 4;
    const CATEGORY_ID_FOURTH = 5;
    const CATEGORY_ID_FIFTH = 6;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDataFactoryInterface
     */
    private $heurekaCategoryDataFactory;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDataFactoryInterface $heurekaCategoryDataFactory
     */
    public function __construct(
        HeurekaCategoryFacade $heurekaCategoryFacade,
        HeurekaCategoryDataFactoryInterface $heurekaCategoryDataFactory
    ) {
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
        $this->heurekaCategoryDataFactory = $heurekaCategoryDataFactory;
    }

    public function load()
    {
        $heurekaCategoriesData = [];

        $firsHeurekaCategoryData = $this->heurekaCategoryDataFactory->create();
        $firsHeurekaCategoryData->id = self::HEUREKA_CATEGORY_ID_FIRST;
        $firsHeurekaCategoryData->name = 'Autobaterie';
        $firsHeurekaCategoryData->fullName = 'Heureka.cz | Auto-moto | Autodoplňky | Autobaterie';

        $heurekaCategoriesData[] = $firsHeurekaCategoryData;

        $secondHeurekaCategoryData = $this->heurekaCategoryDataFactory->create();
        $secondHeurekaCategoryData->id = self::HEUREKA_CATEGORY_ID_SECOND;
        $secondHeurekaCategoryData->name = 'Bublifuky';
        $secondHeurekaCategoryData->fullName = 'Heureka.cz | Dětské zboží | Hračky | Hry na zahradu | Bublifuky';

        $heurekaCategoriesData[] = $secondHeurekaCategoryData;

        $thirdHeurekaCategoryData = $this->heurekaCategoryDataFactory->create();
        $thirdHeurekaCategoryData->id = self::HEUREKA_CATEGORY_ID_THIRD;
        $thirdHeurekaCategoryData->name = 'Cukřenky';
        $thirdHeurekaCategoryData->fullName = 'Heureka.cz | Dům a zahrada | Domácnost | Kuchyně | Stolování | Cukřenky';

        $heurekaCategoriesData[] = $thirdHeurekaCategoryData;

        $this->heurekaCategoryFacade->saveHeurekaCategories($heurekaCategoriesData);

        $heurekaCategoryFirst = $this->heurekaCategoryFacade->getOneById(self::HEUREKA_CATEGORY_ID_FIRST);
        $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId(self::CATEGORY_ID_FIRST, $heurekaCategoryFirst);

        $heurekaCategorySecond = $this->heurekaCategoryFacade->getOneById(self::HEUREKA_CATEGORY_ID_SECOND);
        $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId(self::CATEGORY_ID_SECOND, $heurekaCategorySecond);
        $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId(self::CATEGORY_ID_THIRD, $heurekaCategorySecond);

        $heurekaCategoryThird = $this->heurekaCategoryFacade->getOneById(self::HEUREKA_CATEGORY_ID_THIRD);
        $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId(self::CATEGORY_ID_FOURTH, $heurekaCategoryThird);
        $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId(self::CATEGORY_ID_FIFTH, $heurekaCategoryThird);
    }
}
