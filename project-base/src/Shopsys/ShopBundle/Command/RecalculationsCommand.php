<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecalculationsCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:recalculations';

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository
     */
    private $categoryVisibilityRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator
     */
    private $productHiddenRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator
     */
    private $productPriceRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator
     */
    private $productAvailabilityRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator
     */
    private $productSellingDeniedRecalculator;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository $categoryVisibilityRepository
     * @param \Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator $productHiddenRecalculator
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator
     * @param \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade $productVisibilityFacade
     * @param \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator $productAvailabilityRecalculator
     * @param \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     */
    public function __construct(
        CategoryVisibilityRepository $categoryVisibilityRepository,
        ProductHiddenRecalculator $productHiddenRecalculator,
        ProductPriceRecalculator $productPriceRecalculator,
        ProductVisibilityFacade $productVisibilityFacade,
        ProductAvailabilityRecalculator $productAvailabilityRecalculator,
        ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
    ) {
        $this->categoryVisibilityRepository = $categoryVisibilityRepository;
        $this->productHiddenRecalculator = $productHiddenRecalculator;
        $this->productPriceRecalculator = $productPriceRecalculator;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->productAvailabilityRecalculator = $productAvailabilityRecalculator;
        $this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Run all recalculations.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running recalculations:');
        $output->writeln('<fg=green>Categories visibility.</fg=green>');
        $this->categoryVisibilityRepository->refreshCategoriesVisibility();

        $output->writeln('<fg=green>Products price.</fg=green>');
        $this->productPriceRecalculator->runAllScheduledRecalculations();

        $output->writeln('<fg=green>Products hiding.</fg=green>');
        $this->productHiddenRecalculator->calculateHiddenForAll();

        $output->writeln('<fg=green>Products visibility.</fg=green>');
        $this->productVisibilityFacade->refreshProductsVisibilityForMarked();

        $output->writeln('<fg=green>Products price again (because of variants).</fg=green>');
        // Main variant is set for recalculations after change of variants visibility.
        $this->productPriceRecalculator->runAllScheduledRecalculations();

        $output->writeln('<fg=green>Products availability.</fg=green>');
        $this->productAvailabilityRecalculator->runAllScheduledRecalculations();

        $output->writeln('<fg=green>Products selling denial.</fg=green>');
        $this->productSellingDeniedRecalculator->calculateSellingDeniedForAll();
    }
}
