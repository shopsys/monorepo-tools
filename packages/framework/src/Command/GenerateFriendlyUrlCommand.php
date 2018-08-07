<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFriendlyUrlCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:generate:friendly-url';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade
     */
    private $friendlyUrlGeneratorFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlGeneratorFacade $friendlyUrlGeneratorFacade
     */
    public function __construct(FriendlyUrlGeneratorFacade $friendlyUrlGeneratorFacade)
    {
        $this->friendlyUrlGeneratorFacade = $friendlyUrlGeneratorFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate friendly urls for supported entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=green>Start of generating missing friendly urls from routing_friendly_url.yml file.</fg=green>');

        $this->friendlyUrlGeneratorFacade->generateUrlsForSupportedEntities($output);

        $output->writeln('<fg=green>Generating complete.</fg=green>');
    }
}
