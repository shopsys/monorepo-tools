<?php

namespace Shopsys\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFriendlyUrlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:generate:friendly-url')
            ->setDescription('Generate friendly urls for supported entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $friendlyUrlGeneratorFacade = $this->getContainer()
            ->get('shopsys.shop.component.router.friendly_url.friendly_url_generator_facade');
        /* @var $friendlyUrlGeneratorFacade \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade */

        $output->writeln('<fg=green>Start of generating missing friendly urls from routing_friendly_url.yml file.</fg=green>');

        $friendlyUrlGeneratorFacade->generateUrlsForSupportedEntities($output);

        $output->writeln('<fg=green>Generating complete.</fg=green>');
    }
}
