<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\DataFixtures\Performance\CategoryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Performance\OrderDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Performance\ProductDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Performance\UserDataFixture;
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
     * @var \Shopsys\FrameworkBundle\DataFixtures\Performance\CategoryDataFixture
     */
    private $categoryDataFixture;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Performance\ProductDataFixture
     */
    private $productDataFixture;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Performance\UserDataFixture
     */
    private $userDataFixture;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Performance\OrderDataFixture
     */
    private $orderDataFixture;

    /**
     * @param \Shopsys\FrameworkBundle\DataFixtures\Performance\CategoryDataFixture $categoryDataFixture
     * @param \Shopsys\FrameworkBundle\DataFixtures\Performance\ProductDataFixture $productDataFixture
     * @param \Shopsys\FrameworkBundle\DataFixtures\Performance\UserDataFixture $userDataFixture
     * @param \Shopsys\FrameworkBundle\DataFixtures\Performance\OrderDataFixture $orderDataFixture
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
