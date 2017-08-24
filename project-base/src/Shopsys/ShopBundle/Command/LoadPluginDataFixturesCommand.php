<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadPluginDataFixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:plugin-data-fixtures:load')
            ->setDescription('Loads data fixtures of all registered plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginDataFixtureFacade = $this->getContainer()->get('shopsys.shop.component.plugin.plugin_data_fixture_facade');
        /** @var \Shopsys\ShopBundle\Component\Plugin\PluginDataFixtureFacade $pluginDataFixtureFacade */
        $pluginDataFixtureFacade->loadAll();
    }
}
