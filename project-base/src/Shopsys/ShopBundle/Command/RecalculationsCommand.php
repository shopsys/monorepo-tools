<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecalculationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:recalculations')
            ->setDescription('Run all recalculations.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categoryVisibilityRepository = $this->getContainer()
            ->get('shopsys.shop.category.category_visibility_repository');
        /* @var $categoryVisibilityRepository \Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository */
        $productHiddenRecalculator = $this->getContainer()
            ->get('shopsys.shop.product.product_hidden_recalculator');
        /* @var $productHiddenRecalculator \Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator */
        $productPriceRecalculator = $this->getContainer()
            ->get('shopsys.shop.product.pricing.product_price_recalculator');
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
        $productVisibilityFacade = $this->getContainer()
            ->get('shopsys.shop.product.product_visibility_facade');
        /* @var $productVisibilityFacade \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade */
        $productAvailabilityRecalculator = $this->getContainer()
            ->get('shopsys.shop.product.availability.product_availability_recalculator');
        /* @var $productAvailabilityRecalculator \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */
        $productSellingDeniedRecalculator = $this->getContainer()
            ->get('shopsys.shop.product.product_selling_denied_recalculator');
        /* @var $productSellingDeniedRecalculator \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */

        $output->writeln('Running recalculations:');
        $output->writeln('<fg=green>Categories visibility.</fg=green>');
        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $output->writeln('<fg=green>Products price.</fg=green>');
        $productPriceRecalculator->runAllScheduledRecalculations();

        $output->writeln('<fg=green>Products hiding.</fg=green>');
        $productHiddenRecalculator->calculateHiddenForAll();

        $output->writeln('<fg=green>Products visibility.</fg=green>');
        $productVisibilityFacade->refreshProductsVisibilityForMarked();

        $output->writeln('<fg=green>Products price again (because of variants).</fg=green>');
        // Main variant is set for recalculations after change of variants visibility.
        $productPriceRecalculator->runAllScheduledRecalculations();

        $output->writeln('<fg=green>Products availability.</fg=green>');
        $productAvailabilityRecalculator->runAllScheduledRecalculations();

        $output->writeln('<fg=green>Products selling denial.</fg=green>');
        $productSellingDeniedRecalculator->calculateSellingDeniedForAll();
    }
}
