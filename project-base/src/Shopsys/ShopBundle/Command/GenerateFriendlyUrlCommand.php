<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFriendlyUrlCommand extends Command
{

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade
     */
    private $friendlyUrlGeneratorFacade;

    /**
     * @param \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade $friendlyUrlGeneratorFacade
     */
    public function __construct(FriendlyUrlGeneratorFacade $friendlyUrlGeneratorFacade)
    {
        $this->friendlyUrlGeneratorFacade = $friendlyUrlGeneratorFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('shopsys:generate:friendly-url')
            ->setDescription('Generate friendly urls for supported entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=green>Start of generating missing friendly urls from routing_friendly_url.yml file.</fg=green>');

        $this->friendlyUrlGeneratorFacade->generateUrlsForSupportedEntities($output);

        $output->writeln('<fg=green>Generating complete.</fg=green>');
    }
}
