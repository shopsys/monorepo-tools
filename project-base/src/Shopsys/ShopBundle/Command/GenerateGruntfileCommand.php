<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Css\CssFacade;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class GenerateGruntfileCommand extends Command
{

    /**
     * @var string
     */
    private $stylesDirectory;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Shopsys\ShopBundle\Component\Css\CssFacade
     */
    private $cssFacade;

    /**
     * @param string $stylesDirectory
     * @param string $rootDirectory
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     * @param \Twig\Environment $twig
     * @param \Shopsys\ShopBundle\Component\Css\CssFacade $cssFacade
     */
    public function __construct(
        $stylesDirectory,
        $rootDirectory,
        Domain $domain,
        Environment $twig,
        CssFacade $cssFacade
    ) {
        $this->stylesDirectory = $stylesDirectory;
        $this->rootDirectory = $rootDirectory;
        $this->domain = $domain;
        $this->twig = $twig;
        $this->cssFacade = $cssFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('shopsys:generate:gruntfile')
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
            'rootStylesDirectory' => $this->stylesDirectory,
            'cssVersion' => $cssVersion,
        ]);
        $path = $this->rootDirectory;
        file_put_contents($path . '/Gruntfile.js', $gruntfileContents);
        $output->writeln('<fg=green>Gruntfile.js was successfully generated.</fg=green>');
    }
}
