<?php

namespace Shopsys\ShopBundle\DataFixtures\Performance;

use Faker\Generator as Faker;
use Shopsys\ShopBundle\Component\Console\ProgressBar;
use Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryDataFixture
{
    const FIRST_PERFORMANCE_CATEGORY = 'first_performance_category';

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade
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
     * @var \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @param int[] $categoryCountsByLevel
     * @param \Shopsys\ShopBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade $sqlLoggerFacade
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Faker\Generator $faker
     */
    public function __construct(
        $categoryCountsByLevel,
        CategoryFacade $categoryFacade,
        SqlLoggerFacade $sqlLoggerFacade,
        PersistentReferenceFacade $persistentReferenceFacade,
        Faker $faker
    ) {
        $this->categoryCountsByLevel = $categoryCountsByLevel;
        $this->categoryFacade = $categoryFacade;
        $this->sqlLoggerFacade = $sqlLoggerFacade;
        $this->faker = $faker;
        $this->categoriesCreated = 0;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function load(OutputInterface $output)
    {
        $progressBar = $this->createProgressBar($output);

        $rootCategory = $this->categoryFacade->getRootCategory();
        $this->sqlLoggerFacade->temporarilyDisableLogging();
        $this->recursivelyCreateCategoryTree($rootCategory, $progressBar);
        $this->sqlLoggerFacade->reenableLogging();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $parentCategory
     * @param \Shopsys\ShopBundle\Component\Console\ProgressBar $progressBar
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
     * @param \Shopsys\ShopBundle\Model\Category\Category $parentCategory
     * @return \Shopsys\ShopBundle\Model\Category\CategoryData
     */
    private function getRandomCategoryDataByParentCategory(Category $parentCategory)
    {
        $categoryData = new CategoryData();
        $categoryName = $this->faker->word . ' #' . $this->categoriesCreated;
        $categoryData->name = ['cs' => $categoryName, 'en' => $categoryName];
        $categoryData->descriptions = [
            1 => $this->faker->paragraph(3, false),
            2 => $this->faker->paragraph(3, false),
        ];
        $categoryData->parent = $parentCategory;

        return $categoryData;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Shopsys\ShopBundle\Component\Console\ProgressBar
     */
    private function createProgressBar(OutputInterface $output)
    {
        $progressBar = new ProgressBar($output, $this->recursivelyCountCategoriesInCategoryTree());
        $progressBar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%%,%speed:6.1f% cat./s (%step_duration:.3f% s/cat.),'
            . ' Elapsed: %elapsed_hms%, Remaining: %remaining_hms%, MEM:%memory:9s%'
        );
        $progressBar->setRedrawFrequency(10);
        $progressBar->start();

        return $progressBar;
    }
}
