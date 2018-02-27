<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadPluginDataFixturesCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:plugin-data-fixtures:load';

    /**
     * @var \Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureFacade
     */
    private $pluginDataFixtureFacade;

    /**
     * @param \Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureFacade $pluginDataFixtureFacade
     */
    public function __construct(PluginDataFixtureFacade $pluginDataFixtureFacade)
    {
        $this->pluginDataFixtureFacade = $pluginDataFixtureFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Loads data fixtures of all registered plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pluginDataFixtureFacade->loadAll();
    }
}
