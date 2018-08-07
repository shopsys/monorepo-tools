<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Css\CssFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class GenerateGruntfileCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:generate:gruntfile';

    /**
     * @var string
     */
    private $customResourcesDirectory;

    /**
     * @var string
     */
    private $frameworkResourcesDirectory;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Css\CssFacade
     */
    private $cssFacade;

    /**
     * @param string $customResourcesDirectory
     * @param string $frameworkResourcesDirectory
     * @param string $rootDirectory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Twig\Environment $twig
     * @param \Shopsys\FrameworkBundle\Component\Css\CssFacade $cssFacade
     */
    public function __construct(
        string $customResourcesDirectory,
        string $frameworkResourcesDirectory,
        string $rootDirectory,
        Domain $domain,
        Environment $twig,
        CssFacade $cssFacade
    ) {
        $this->customResourcesDirectory = $customResourcesDirectory;
        $this->frameworkResourcesDirectory = $frameworkResourcesDirectory;
        $this->rootDirectory = $rootDirectory;
        $this->domain = $domain;
        $this->twig = $twig;
        $this->cssFacade = $cssFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate Gruntfile.js by domain settings');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cssVersion = time();
        $this->cssFacade->setCssVersion($cssVersion);

        $output->writeln('Start of generating Gruntfile.js.');
        $gruntfileContents = $this->twig->render('@ShopsysShop/Grunt/gruntfile.js.twig', [
            'domains' => $this->domain->getAll(),
            'customResourcesDirectory' => $this->customResourcesDirectory,
            'frameworkResourcesDirectory' => $this->frameworkResourcesDirectory,
            'cssVersion' => $cssVersion,
        ]);
        $path = $this->rootDirectory;
        file_put_contents($path . '/Gruntfile.js', $gruntfileContents);
        $output->writeln('<fg=green>Gruntfile.js was successfully generated.</fg=green>');
    }
}
