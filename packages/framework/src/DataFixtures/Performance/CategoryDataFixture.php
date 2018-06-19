<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Performance;

use Faker\Generator as Faker;
use Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryDataFixture
{
    const FIRST_PERFORMANCE_CATEGORY = 'first_performance_category';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory
     */
    private $categoryDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade
     */
    private $sqlLoggerFacade;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var int[]
     */
    private $categoryCountsByLevel;

    /**
     * @var int
     */
    private $categoriesCreated;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory
     */
    private $progressBarFactory;

    /**
     * @param int[] $categoryCountsByLevel
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory $categoryDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade $sqlLoggerFacade
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Faker\Generator $faker
     * @param \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory $progressBarFactory
     */
    public function __construct(
        $categoryCountsByLevel,
        CategoryDataFactory $categoryDataFactory,
        CategoryFacade $categoryFacade,
        SqlLoggerFacade $sqlLoggerFacade,
        PersistentReferenceFacade $persistentReferenceFacade,
        Faker $faker,
        ProgressBarFactory $progressBarFactory
    ) {
        $this->categoryCountsByLevel = $categoryCountsByLevel;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->categoryFacade = $categoryFacade;
        $this->sqlLoggerFacade = $sqlLoggerFacade;
        $this->faker = $faker;
        $this->categoriesCreated = 0;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->progressBarFactory = $progressBarFactory;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function load(OutputInterface $output)
    {
        $progressBar = $this->progressBarFactory->create($output, array_sum($this->categoryCountsByLevel));

        $rootCategory = $this->categoryFacade->getRootCategory();
        $this->sqlLoggerFacade->temporarilyDisableLogging();
        $this->recursivelyCreateCategoryTree($rootCategory, $progressBar);
        $this->sqlLoggerFacade->reenableLogging();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $parentCategory
     * @param \Shopsys\FrameworkBundle\Component\Console\ProgressBar $progressBar
     * @param int $categoryLevel
     */
    private function recursivelyCreateCategoryTree($parentCategory, ProgressBar $progressBar, $categoryLevel = 0)
    {
        for ($i = 0; $i < $this->categoryCountsByLevel[$categoryLevel]; $i++) {
            $categoryData = $this->getRandomCategoryDataByParentCategory($parentCategory);
            $newCategory = $this->categoryFacade->create($categoryData);
            $progressBar->advance();
            $this->categoriesCreated++;
            if ($this->categoriesCreated === 1) {
                $this->persistentReferenceFacade->persistReference(self::FIRST_PERFORMANCE_CATEGORY, $newCategory);
            }
            if (array_key_exists($categoryLevel + 1, $this->categoryCountsByLevel)) {
                $this->recursivelyCreateCategoryTree($newCategory, $progressBar, $categoryLevel + 1);
            }
        }
    }

    /**
     * @param int $categoryLevel
     * @return int
     */
    private function recursivelyCountCategoriesInCategoryTree($categoryLevel = 0)
    {
        $count = 0;
        for ($i = 0; $i < $this->categoryCountsByLevel[$categoryLevel]; $i++) {
            $count++;
            if (array_key_exists($categoryLevel + 1, $this->categoryCountsByLevel)) {
                $count += $this->recursivelyCountCategoriesInCategoryTree($categoryLevel + 1);
            }
        }

        return $count;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $parentCategory
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
     */
    private function getRandomCategoryDataByParentCategory(Category $parentCategory)
    {
        $categoryData = $this->categoryDataFactory->createDefault();
        $categoryName = $this->faker->word . ' #' . $this->categoriesCreated;
        $categoryData->name = ['cs' => $categoryName, 'en' => $categoryName];
        $categoryData->descriptions = [
            1 => $this->faker->paragraph(3, false),
            2 => $this->faker->paragraph(3, false),
        ];
        $categoryData->parent = $parentCategory;

        return $categoryData;
    }
}
