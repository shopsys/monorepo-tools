<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Css\CssFacade;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateGruntfileCommand extends ContainerAwareCommand
{
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
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */
        $twig = $this->getContainer()->get('twig');
        /* @var $twig \Twig_Environment */
        $cssFacade = $this->getContainer()->get(CssFacade::class);
        /* @var $cssFacade \Shopsys\ShopBundle\Component\Css\CssFacade */

        $cssVersion = time();
        $cssFacade->setCssVersion($cssVersion);

        $output->writeln('Start of generating Gruntfile.js.');
        $gruntfileContents = $twig->render('@ShopsysShop/Grunt/gruntfile.js.twig', [
            'domains' => $domain->getAll(),
            'rootStylesDirectory' => $this->getContainer()->getParameter('shopsys.styles_dir'),
            'cssVersion' => $cssVersion,
        ]);
        $path = $this->getContainer()->getParameter('shopsys.root_dir');
        file_put_contents($path . '/Gruntfile.js', $gruntfileContents);
        $output->writeln('<fg=green>Gruntfile.js was successfully generated.</fg=green>');
    }
}
