<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\DataFixtures\Performance\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Performance\OrderDataFixture;
use Shopsys\ShopBundle\DataFixtures\Performance\ProductDataFixture;
use Shopsys\ShopBundle\DataFixtures\Performance\UserDataFixture;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PerformanceDataCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:performance-data';

    /**
     * @var \Shopsys\ShopBundle\DataFixtures\Performance\CategoryDataFixture
     */
    private $categoryDataFixture;

    /**
     * @var \Shopsys\ShopBundle\DataFixtures\Performance\ProductDataFixture
     */
    private $productDataFixture;

    /**
     * @var \Shopsys\ShopBundle\DataFixtures\Performance\UserDataFixture
     */
    private $userDataFixture;

    /**
     * @var \Shopsys\ShopBundle\DataFixtures\Performance\OrderDataFixture
     */
    private $orderDataFixture;

    /**
     * @param \Shopsys\ShopBundle\DataFixtures\Performance\CategoryDataFixture $categoryDataFixture
     * @param \Shopsys\ShopBundle\DataFixtures\Performance\ProductDataFixture $productDataFixture
     * @param \Shopsys\ShopBundle\DataFixtures\Performance\UserDataFixture $userDataFixture
     * @param \Shopsys\ShopBundle\DataFixtures\Performance\OrderDataFixture $orderDataFixture
     */
    public function __construct(
        CategoryDataFixture $categoryDataFixture,
        ProductDataFixture $productDataFixture,
        UserDataFixture $userDataFixture,
        OrderDataFixture $orderDataFixture
    ) {
        $this->categoryDataFixture = $categoryDataFixture;
        $this->productDataFixture = $productDataFixture;
        $this->userDataFixture = $userDataFixture;
        $this->orderDataFixture = $orderDataFixture;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Import performance data to test db. Demo and base data fixtures must be imported first.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=green>loading ' . CategoryDataFixture::class . '</fg=green>');
        $this->categoryDataFixture->load($output);
        $output->writeln('<fg=green>loading ' . ProductDataFixture::class . '</fg=green>');
        $this->productDataFixture->load($output);
        $output->writeln('<fg=green>loading ' . UserDataFixture::class . '</fg=green>');
        $this->userDataFixture->load($output);
        $output->writeln('<fg=green>loading ' . OrderDataFixture::class . '</fg=green>');
        $this->orderDataFixture->load($output);
    }
}
